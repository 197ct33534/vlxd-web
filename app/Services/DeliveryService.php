<?php

namespace App\Services;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductUnitConversion;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    public function createDeliveryNote(Order $order, array $data): DeliveryNote
    {
        return DB::transaction(function () use ($order, $data) {
            $note = DeliveryNote::create([
                'code' => $this->nextCode(),
                'order_id' => $order->id,
                'project_id' => $order->project_id,
                'driver_id' => $data['driver_id'] ?? null,
                'license_plate' => $data['license_plate'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? now()->toDateString(),
                'status' => 'pending',
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'driver_collected_cash' => (bool) ($data['driver_collected_cash'] ?? false),
                'is_direct_delivery' => (bool) ($data['is_direct_delivery'] ?? false),
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['items'] as $row) {
                $qty = round((float) ($row['quantity'] ?? 0), 3);
                if ($qty <= 0) {
                    continue;
                }

                $orderItem = OrderItem::where('order_id', $order->id)->findOrFail($row['order_item_id']);
                $overridden = filter_var($row['is_overridden'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $converted = $overridden
                    ? round((float) ($row['converted_quantity'] ?? 0), 3)
                    : $this->convert($orderItem, $qty, $row['product_id'] ?? null);

                $price = round((float) ($row['unit_price'] ?? $orderItem->price), 2);

                $note->items()->create([
                    'order_item_id' => $orderItem->id,
                    'product_id' => $row['product_id'] ?? null,
                    'product_name' => $orderItem->product_name,
                    'trade_unit' => $row['trade_unit'] ?? $orderItem->unit,
                    'quantity' => $qty,
                    'converted_quantity' => $converted,
                    'is_overridden' => $overridden,
                    'unit_price' => $price,
                    'total_amount' => round($qty * $price, 2),
                ]);
            }

            return $note->load('items');
        });
    }

    public function checkAndUpdateOrderStatus(Order $order): void
    {
        $ordered = round((float) $order->items()->sum('quantity'), 3);
        $delivered = round((float) DeliveryNoteItem::query()
            ->whereIn('delivery_note_id', $order->deliveryNotes()->where('status', 'delivered')->select('id'))
            ->sum('quantity'), 3);

        if ($delivered <= 0 || $order->status === 'cancelled') {
            return;
        }

        $order->update([
            'status' => $delivered >= $ordered ? 'delivered' : 'partially_delivered',
            'delivered_at' => $delivered >= $ordered ? now() : $order->delivered_at,
        ]);
    }

    public function completeDelivery(DeliveryNote $note, ?string $photoUrl, $lat, $lng): DeliveryNote
    {
        return DB::transaction(function () use ($note, $photoUrl, $lat, $lng) {
            $note = DeliveryNote::whereKey($note->id)->lockForUpdate()->first();
            if ($note->status === 'delivered') {
                return $note->load(['items', 'order']);
            }

            $note->update([
                'status' => 'delivered',
                'photo_url' => $photoUrl,
                'lat' => $lat,
                'lng' => $lng,
            ]);

            if ((float) $note->shipping_fee > 0 && !$note->driver_collected_cash && $note->project_id) {
                Invoice::create([
                    'project_id' => $note->project_id,
                    'code' => 'PHI-' . $note->code,
                    'invoice_date' => now()->toDateString(),
                    'total_amount' => $note->shipping_fee,
                    'note' => 'Phí hạ hàng / vận chuyển chuyến ' . $note->code,
                    'vat_rate' => 0,
                    'vat_amount' => 0,
                ]);
                app(ProjectDebtService::class)->recalculate($note->project_id);
            }

            $warnings = app(StockService::class)->exportFromDeliveryNote($note);
            $this->checkAndUpdateOrderStatus($note->order);

            $fresh = $note->fresh(['items', 'order']);
            $fresh->setAttribute('stock_warnings', $warnings);

            return $fresh;
        });
    }

    private function convert(OrderItem $item, float $qty, $productId): float
    {
        if (!$productId) {
            return round($qty, 3);
        }

        $factor = ProductUnitConversion::query()
            ->where('product_id', $productId)
            ->where('unit_name', $item->unit)
            ->value('conversion_factor');

        return round($qty * (float) ($factor ?: 1), 3);
    }

    private function nextCode(): string
    {
        $seq = DeliveryNote::whereDate('created_at', today())->count() + 1;

        return 'PGH-' . now()->format('Ymd') . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
