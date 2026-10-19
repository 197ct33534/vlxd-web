<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Services\DeliveryService;
use App\Services\ShopNotifier;
use App\Services\ZaloNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $notes = DeliveryNote::with(['order.customer', 'project', 'items'])
            ->whereIn('status', ['pending', 'delivering'])
            ->when(!(Auth::user()?->isAdmin() && !request()->boolean('mine')), function ($q) {
                $q->where('driver_id', Auth::id());
            })
            ->orderByRaw("CASE status WHEN 'delivering' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->latest()
            ->get();

        return view('driver.index', compact('notes'));
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $this->authorizeDriver($deliveryNote);
        $deliveryNote->load(['order.customer', 'project', 'items']);

        return view('driver.show', compact('deliveryNote'));
    }

    public function start(DeliveryNote $deliveryNote, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        $this->authorizeDriver($deliveryNote);

        if (!in_array($deliveryNote->status, ['pending', 'delivering'], true)) {
            return back()->with('error', 'Không thể bắt đầu chuyến này.');
        }

        $deliveryNote->update(['status' => 'delivering']);

        $order = $deliveryNote->order->fresh('customer');
        $zalo->notifyOrderStatus($order, 'delivering');
        $shop->notifyStatus($order, 'delivering');

        return back()->with('success', 'Đã bắt đầu giao hàng.');
    }

    public function complete(Request $request, DeliveryNote $deliveryNote, DeliveryService $deliveries, ZaloNotifier $zalo, ShopNotifier $shop)
    {
        $this->authorizeDriver($deliveryNote);

        $data = $request->validate([
            'delivery_photo' => 'nullable|image|max:5120',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
        ]);

        $photoPath = $deliveryNote->photo_url;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('deliveries', 'public');
        }

        $note = $deliveries->completeDelivery(
            $deliveryNote,
            $photoPath,
            $data['delivery_lat'] ?? null,
            $data['delivery_lng'] ?? null
        );

        $order = $note->order->fresh('customer');
        $zalo->notifyOrderStatus($order, $order->status);
        $shop->notifyStatus($order, $order->status);

        $message = 'Đã giao xong ' . $note->code;
        $warnings = $note->stock_warnings ?? [];
        if ($warnings) {
            $message .= '. Cảnh báo tồn âm: ' . implode('; ', $warnings);
        }

        return redirect()->route('driver.index')->with('success', $message);
    }

    private function authorizeDriver(DeliveryNote $note): void
    {
        $user = Auth::user();
        if ($user?->isAdmin()) {
            return;
        }
        if ($note->driver_id !== Auth::id()) {
            abort(403);
        }
    }
}
