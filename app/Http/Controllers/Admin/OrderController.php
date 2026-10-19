<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Project;
use App\Models\User;
use App\Services\CreditCheckService;
use App\Services\DeliveryService;
use App\Services\ProjectDebtService;
use App\Services\ShopNotifier;
use App\Services\ZaloNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['customer', 'project', 'driver', 'invoice'])
            ->when($status === 'need_invoice', function ($q) {
                $q->whereNull('invoice_id')
                    ->whereIn('status', ['confirmed', 'delivering', 'delivered']);
            })
            ->when($status && $status !== 'need_invoice', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(30);

        return view('admin.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer', 'project', 'items', 'driver', 'confirmer', 'invoice',
            'deliveryNotes.driver', 'deliveryNotes.items',
        ]);
        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $projects = Project::where('customer_id', $order->customer_id)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $credit = $order->project
            ? app(CreditCheckService::class)->checkCreditStatus($order->project)
            : ['is_exceeded' => false, 'reason' => '', 'details' => []];

        return view('admin.orders.show', compact('order', 'drivers', 'projects', 'products', 'credit'));
    }

    public function updateProject(Request $request, Order $order)
    {
        if ($order->invoice_id) {
            return back()->with('error', 'Đơn đã xuất hóa đơn — không đổi công trình.');
        }

        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::where('id', $data['project_id'])
            ->where('customer_id', $order->customer_id)
            ->firstOrFail();

        $order->update([
            'project_id' => $project->id,
            'delivery_address' => $order->delivery_address ?: $project->address,
        ]);

        return back()->with('success', 'Đã gắn công trình ' . $project->name);
    }

    public function updateItems(Request $request, Order $order)
    {
        if (!in_array($order->status, ['pending_confirm', 'draft', 'confirmed'], true)) {
            return back()->with('error', 'Không thể sửa giá đơn ở trạng thái này.');
        }

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($order, $data) {
            $total = 0;
            foreach ($data['items'] as $row) {
                $item = OrderItem::where('order_id', $order->id)->findOrFail($row['id']);
                $qty = (float) $row['quantity'];
                $price = (float) $row['price'];
                $amount = round($qty * $price, 2);
                $item->update([
                    'quantity' => $qty,
                    'price' => $price,
                    'amount' => $amount,
                ]);
                $total += $amount;
            }
            $order->update(['total_amount' => $total]);
        });

        return back()->with('success', 'Đã cập nhật giá / số lượng.');
    }

    public function confirm(Request $request, Order $order, ZaloNotifier $zalo, ShopNotifier $shop, CreditCheckService $credit)
    {
        if (!in_array($order->status, ['pending_confirm', 'draft'], true)) {
            return back()->with('error', 'Đơn không ở trạng thái chờ xác nhận.');
        }

        if ($order->project_id) {
            $status = $credit->checkCreditStatus($order->project);
            if ($status['is_exceeded'] && !$request->boolean('credit_confirmed')) {
                return back()->with('error', $status['reason'] . '. Tick xác nhận admin để tiếp tục cấp hàng.');
            }
        }

        $order->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        $fresh = $order->fresh('customer');
        $zalo->notifyOrderStatus($fresh, 'confirmed');
        $shop->notifyStatus($fresh, 'confirmed');

        return back()->with('success', 'Đã xác nhận đơn ' . $order->code);
    }

    public function assignDriver(Request $request, Order $order)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $driver = User::where('id', $data['driver_id'])->where('role', 'driver')->firstOrFail();

        $order->update([
            'driver_id' => $driver->id,
            'status' => $order->status === 'confirmed' ? 'confirmed' : $order->status,
        ]);

        return back()->with('success', 'Đã gán tài xế ' . $driver->name);
    }

    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['delivered', 'cancelled'], true)) {
            return back()->with('error', 'Không thể hủy đơn này.');
        }

        $order->update([
            'status' => 'cancelled',
            'note' => trim(($order->note ? $order->note . "\n" : '') . 'Hủy: ' . ($request->input('reason') ?? '')),
        ]);

        return back()->with('success', 'Đã hủy đơn.');
    }

    /**
     * Phase 2 plan: đơn confirmed/delivered → sinh Invoice + cập nhật projects.total_*.
     */
    public function exportInvoice(Request $request, Order $order)
    {
        if ($order->invoice_id) {
            return redirect()
                ->route('invoices.show', $order->invoice_id)
                ->with('success', 'Đơn đã có hóa đơn.');
        }

        if (!$order->project_id) {
            return back()->with('error', 'Đơn chưa gắn công trình — không thể xuất hóa đơn.');
        }

        if (!in_array($order->status, ['confirmed', 'delivering', 'partially_delivered', 'delivered'], true)) {
            return back()->with('error', 'Chỉ xuất hóa đơn khi đơn đã xác nhận / đang giao / đã giao.');
        }

        $order->load('items', 'project');

        if ($order->items->isEmpty()) {
            return back()->with('error', 'Đơn không có dòng vật tư.');
        }

        $vatRate = max(0, (float) $request->input('vat_rate', 0));
        $subtotal = round((float) $order->items->sum('amount'), 2);
        $vatAmount = $vatRate > 0 ? round($subtotal * $vatRate / 100, 2) : 0;

        $invoice = DB::transaction(function () use ($order, $vatRate, $subtotal, $vatAmount) {
            $invoice = Invoice::create([
                'project_id' => $order->project_id,
                'code' => 'HD-' . $order->code,
                'invoice_date' => now()->toDateString(),
                'total_amount' => round($subtotal + $vatAmount, 2),
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'note' => 'Từ đơn mobile ' . $order->code
                    . ($order->note ? ' — ' . $order->note : ''),
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'date' => $invoice->invoice_date,
                    'product_name' => $item->product_name,
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'amount' => $item->amount,
                ]);
            }

            $order->update(['invoice_id' => $invoice->id]);

            app(ProjectDebtService::class)->recalculate($order->project_id);

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Đã xuất hóa đơn ' . $invoice->code . ' từ đơn ' . $order->code);
    }

    public function storeDeliveryNote(Request $request, Order $order, DeliveryService $deliveries)
    {
        if (!$order->project_id) {
            return back()->with('error', 'Cần gắn công trình trước khi tạo chuyến.');
        }

        if (!in_array($order->status, ['confirmed', 'delivering', 'partially_delivered'], true)) {
            return back()->with('error', 'Chỉ tạo chuyến khi đơn đã xác nhận.');
        }

        $data = $request->validate([
            'driver_id' => 'nullable|exists:users,id',
            'license_plate' => 'nullable|string|max:20',
            'delivery_date' => 'nullable|date',
            'shipping_fee' => 'nullable|numeric|min:0',
            'driver_collected_cash' => 'nullable|boolean',
            'is_direct_delivery' => 'nullable|boolean',
            'note' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.converted_quantity' => 'nullable|numeric|min:0',
            'items.*.is_overridden' => 'nullable|boolean',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.trade_unit' => 'nullable|string|max:50',
        ]);

        $data['driver_collected_cash'] = $request->boolean('driver_collected_cash');
        $data['is_direct_delivery'] = $request->boolean('is_direct_delivery');

        $note = $deliveries->createDeliveryNote($order, $data);
        if ($note->items->isEmpty()) {
            $note->delete();
            return back()->with('error', 'Chưa có dòng nào có số lượng giao.');
        }

        return back()->with('success', 'Đã tạo chuyến ' . $note->code);
    }
}
