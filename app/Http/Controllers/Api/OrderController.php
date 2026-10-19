<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\Order;
use App\Services\DeliveryService;
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
            $notes = DeliveryNote::with(['order.customer:id,name,phone', 'project:id,name', 'items'])
                ->where('driver_id', $user->id);

            if ($request->filled('status')) {
                $notes->where('status', $request->query('status'));
            }

            return response()->json($notes->latest()->paginate(50));
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

    public function start(Request $request, DeliveryNote $deliveryNote, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        if ($deny = $this->authorizeNote($request, $deliveryNote)) {
            return $deny;
        }

        if (!in_array($deliveryNote->status, ['pending', 'delivering'], true)) {
            return response()->json(['message' => 'Không thể bắt đầu chuyến này.'], 422);
        }

        $deliveryNote->update(['status' => 'delivering']);
        $order = $deliveryNote->order->fresh('customer');
        $zalo->notifyOrderStatus($order, 'delivering');
        $shop->notifyStatus($order, 'delivering');

        return response()->json($deliveryNote->fresh(['items', 'order']));
    }

    public function complete(Request $request, DeliveryNote $deliveryNote, DeliveryService $deliveries, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        if ($deny = $this->authorizeNote($request, $deliveryNote)) {
            return $deny;
        }

        $data = $request->validate([
            'delivery_photo' => 'nullable|image|max:5120',
            'photo' => 'nullable|image|max:5120',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $photoPath = $deliveryNote->photo_url;
        $file = $request->file('delivery_photo') ?? $request->file('photo');
        if ($file) {
            $photoPath = $file->store('deliveries', 'public');
        }

        $note = $deliveries->completeDelivery(
            $deliveryNote,
            $photoPath,
            $data['delivery_lat'] ?? $data['lat'] ?? null,
            $data['delivery_lng'] ?? $data['lng'] ?? null
        );

        $order = $note->order->fresh('customer');
        $zalo->notifyOrderStatus($order, $order->status);
        $shop->notifyStatus($order, $order->status);

        return response()->json($note->load(['items', 'order']));
    }

    private function authorizeNote(Request $request, DeliveryNote $note)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return null;
        }

        if ($user->isDriver() && $note->driver_id === $user->id) {
            return null;
        }

        return response()->json(['message' => 'Forbidden'], 403);
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
