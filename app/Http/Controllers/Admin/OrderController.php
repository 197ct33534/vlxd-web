<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Project;
use App\Models\User;
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
        $order->load(['customer', 'project', 'items', 'driver', 'confirmer', 'invoice']);
        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $projects = Project::where('customer_id', $order->customer_id)->orderBy('name')->get();

        return view('admin.orders.show', compact('order', 'drivers', 'projects'));
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

    public function confirm(Order $order, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        if (!in_array($order->status, ['pending_confirm', 'draft'], true)) {
            return back()->with('error', 'Đơn không ở trạng thái chờ xác nhận.');
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
    public function exportInvoice(Order $order)
    {
        if ($order->invoice_id) {
            return redirect()
                ->route('invoices.show', $order->invoice_id)
                ->with('success', 'Đơn đã có hóa đơn.');
        }

        if (!$order->project_id) {
            return back()->with('error', 'Đơn chưa gắn công trình — không thể xuất hóa đơn.');
        }

        if (!in_array($order->status, ['confirmed', 'delivering', 'delivered'], true)) {
            return back()->with('error', 'Chỉ xuất hóa đơn khi đơn đã xác nhận / đang giao / đã giao.');
        }

        $order->load('items', 'project');

        if ($order->items->isEmpty()) {
            return back()->with('error', 'Đơn không có dòng vật tư.');
        }

        $invoice = DB::transaction(function () use ($order) {
            $invoice = new Invoice();
            $invoice->forceFill([
                'project_id' => $order->project_id,
                'code' => 'HD-' . $order->code,
                'invoice_date' => now()->toDateString(),
                'total_amount' => $order->total_amount,
                'note' => 'Từ đơn mobile ' . $order->code
                    . ($order->note ? ' — ' . $order->note : ''),
            ])->save();

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

            $project = $order->project;
            $totalInvoices = Invoice::where('project_id', $project->id)->sum('total_amount');
            $project->forceFill([
                'total_invoice' => $totalInvoices,
                'total_debt' => max(0, $totalInvoices - (float) $project->total_paid),
            ])->save();

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Đã xuất hóa đơn ' . $invoice->code . ' từ đơn ' . $order->code);
    }
}
