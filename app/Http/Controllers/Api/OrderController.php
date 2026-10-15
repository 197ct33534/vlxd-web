<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ShopNotifier;
use App\Services\ZaloNotifier;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::with(['customer:id,name,phone', 'project:id,name', 'items']);

        if ($user->isDriver()) {
            $query->where('driver_id', $user->id);
        } elseif (!$user->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->latest()->paginate(50));
    }

    public function show(Request $request, Order $order)
    {
        if ($deny = $this->authorizeOrder($request, $order)) {
            return $deny;
        }

        $order->load(['customer', 'project', 'items', 'driver:id,name']);

        return response()->json($order);
    }

    public function start(Request $request, Order $order, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        if ($deny = $this->authorizeOrder($request, $order, true)) {
            return $deny;
        }

        if (!in_array($order->status, ['confirmed', 'delivering'], true)) {
            return response()->json(['message' => 'Không thể bắt đầu giao đơn này.'], 422);
        }

        $order->update([
            'status' => 'delivering',
            'delivering_at' => now(),
        ]);

        $fresh = $order->fresh('customer');
        $zalo->notifyOrderStatus($fresh, 'delivering');
        $shop->notifyStatus($fresh, 'delivering');

        return response()->json($fresh->load(['customer', 'project', 'items']));
    }

    public function complete(Request $request, Order $order, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        if ($deny = $this->authorizeOrder($request, $order, true)) {
            return $deny;
        }

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

        return response()->json($fresh->load(['customer', 'project', 'items']));
    }

    private function authorizeOrder(Request $request, Order $order, bool $driverAction = false)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return null;
        }

        if ($user->isDriver()) {
            if ($order->driver_id !== $user->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return null;
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
