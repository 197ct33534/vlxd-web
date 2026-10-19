<?php

namespace App\Services;

use App\Models\DeliveryNote;
use App\Models\Product;
use App\Models\StockMove;
use App\Models\StockReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public function importStock(array $receiptData, array $items): StockReceipt
    {
        return DB::transaction(function () use ($receiptData, $items) {
            $receipt = StockReceipt::create([
                'code' => $this->nextCode(),
                'supplier_id' => $receiptData['supplier_id'],
                'receipt_date' => $receiptData['receipt_date'] ?? now()->toDateString(),
                'total_amount' => 0,
                'note' => $receiptData['note'] ?? null,
            ]);

            $total = 0;
            foreach ($items as $row) {
                $qty = round((float) ($row['quantity'] ?? 0), 3);
                if ($qty <= 0) {
                    continue;
                }
                $price = round((float) ($row['unit_price'] ?? 0), 2);
                $line = round($qty * $price, 2);
                $total += $line;

                $receipt->items()->create([
                    'product_id' => $row['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_amount' => $line,
                ]);

                $product = Product::whereKey($row['product_id'])->lockForUpdate()->firstOrFail();
                $oldQty = (float) $product->stock_quantity;
                $oldCost = (float) $product->avg_cost;
                $newCost = $oldQty > 0
                    ? round((($oldQty * $oldCost) + ($qty * $price)) / ($oldQty + $qty), 2)
                    : $price;
                $newQty = round($oldQty + $qty, 3);

                $product->update([
                    'stock_quantity' => $newQty,
                    'avg_cost' => $newCost,
                ]);

                StockMove::create([
                    'product_id' => $product->id,
                    'type' => 'import',
                    'reference_type' => 'stock_receipt',
                    'reference_id' => $receipt->id,
                    'quantity' => $qty,
                    'unit_cost' => $price,
                    'balance_after' => $newQty,
                    'note' => $receipt->code,
                    'created_at' => now(),
                ]);
            }

            $receipt->update(['total_amount' => round($total, 2)]);

            return $receipt->load('items');
        });
    }

    /**
     * @return list<string> cảnh báo tồn âm
     */
    public function exportFromDeliveryNote(DeliveryNote $deliveryNote): array
    {
        if ($deliveryNote->is_direct_delivery) {
            return [];
        }

        if (StockMove::where('reference_type', 'delivery_note')->where('reference_id', $deliveryNote->id)->exists()) {
            return [];
        }

        $warnings = [];
        $deliveryNote->loadMissing('items');

        foreach ($deliveryNote->items as $line) {
            if (!$line->product_id) {
                continue;
            }

            $qtyOut = round((float) ($line->converted_quantity > 0 ? $line->converted_quantity : $line->quantity), 3);
            if ($qtyOut <= 0) {
                continue;
            }

            $product = Product::whereKey($line->product_id)->lockForUpdate()->first();
            if (!$product) {
                continue;
            }

            $newQty = round((float) $product->stock_quantity - $qtyOut, 3);
            $product->update(['stock_quantity' => $newQty]);

            StockMove::create([
                'product_id' => $product->id,
                'type' => 'export',
                'reference_type' => 'delivery_note',
                'reference_id' => $deliveryNote->id,
                'quantity' => -$qtyOut,
                'unit_cost' => $product->avg_cost,
                'balance_after' => $newQty,
                'note' => $deliveryNote->code,
                'created_at' => now(),
            ]);

            if ($newQty < 0) {
                $message = $product->name . ' tồn âm ' . number_format($newQty, 3, ',', '.');
                $warnings[] = $message;
                Log::warning('Tồn kho âm sau chuyến ' . $deliveryNote->code . ': ' . $message);
            }
        }

        return $warnings;
    }

    private function nextCode(): string
    {
        $seq = StockReceipt::whereDate('created_at', today())->count() + 1;

        return 'PNK-' . now()->format('Ymd') . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
