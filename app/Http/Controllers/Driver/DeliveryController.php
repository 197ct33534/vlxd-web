<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShopNotifier;
use App\Services\ZaloNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'project', 'items'])
            ->where(function ($q) {
                $user = Auth::user();
                if ($user?->isAdmin() && !request()->boolean('mine')) {
                    // Admin xem toàn bộ đơn đang giao / chờ giao
                    $q->whereIn('status', ['confirmed', 'delivering']);
                } else {
                    $q->where('driver_id', Auth::id())
                        ->whereIn('status', ['confirmed', 'delivering']);
                }
            })
            ->orderByRaw("CASE status WHEN 'delivering' THEN 0 WHEN 'confirmed' THEN 1 ELSE 2 END")
            ->latest()
            ->get();

        return view('driver.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeDriver($order);
        $order->load(['customer', 'project', 'items']);

        return view('driver.show', compact('order'));
    }

    public function start(Order $order, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        $this->authorizeDriver($order);

        if (!in_array($order->status, ['confirmed', 'delivering'], true)) {
            return back()->with('error', 'Không thể bắt đầu giao đơn này.');
        }

        $order->update([
            'status' => 'delivering',
            'delivering_at' => now(),
        ]);

        $fresh = $order->fresh('customer');
        $zalo->notifyOrderStatus($fresh, 'delivering');
        $shop->notifyStatus($fresh, 'delivering');

        return back()->with('success', 'Đã bắt đầu giao hàng.');
    }

    public function complete(Request $request, Order $order, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        $this->authorizeDriver($order);

        $data = $request->validate([
            'delivery_photo' => 'nullable|image|max:5120',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
        ]);

        $photoPath = $order->delivery_photo;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('deliveries', 'public');
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'delivery_photo' => $photoPath,
            'delivery_lat' => $data['delivery_lat'] ?? null,
            'delivery_lng' => $data['delivery_lng'] ?? null,
        ]);

        $fresh = $order->fresh('customer');
        $zalo->notifyOrderStatus($fresh, 'delivered');
        $shop->notifyStatus($fresh, 'delivered');

        return redirect()->route('driver.index')->with('success', 'Đã giao xong ' . $order->code);
    }

    private function authorizeDriver(Order $order): void
    {
        $user = Auth::user();
        if ($user?->isAdmin()) {
            return;
        }
        if ($order->driver_id !== Auth::id()) {
            abort(403);
        }
    }
}
