<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 3: fallback nội bộ (Telegram) khi khách đặt đơn / đổi trạng thái.
 * Không có TELEGRAM_* thì chỉ log — an toàn local.
 */
class ShopNotifier
{
    public function notifyNewOrder(Order $order): bool
    {
        $order->loadMissing(['customer', 'project', 'items']);

        $lines = collect($order->items)->map(function ($i) {
            return sprintf(
                '- %s: %s %s × %s',
                $i->product_name,
                $i->quantity,
                $i->unit,
                number_format((float) $i->price, 0, ',', '.')
            );
        })->implode("\n");

        $text = "ĐƠN MỚI {$order->code}\n"
            . "Khách: {$order->customer?->name} ({$order->customer?->phone})\n"
            . "CT: " . ($order->project?->name ?? '—') . "\n"
            . "Giao: " . ($order->delivery_address ?? '—') . "\n"
            . "Tổng: " . number_format((float) $order->total_amount, 0, ',', '.') . " đ\n"
            . $lines . "\n"
            . url('/admin/orders/' . $order->id);

        return $this->send($text, [
            'event' => 'new_order',
            'order_code' => $order->code,
        ]);
    }

    public function notifyStatus(Order $order, string $event): bool
    {
        $order->loadMissing('customer');

        $text = "Đơn {$order->code} → {$order->statusLabel()}\n"
            . "Khách: {$order->customer?->name} ({$order->customer?->phone})\n"
            . "Sự kiện: {$event}\n"
            . url('/admin/orders/' . $order->id);

        return $this->send($text, [
            'event' => $event,
            'order_code' => $order->code,
        ]);
    }

    private function send(string $text, array $context = []): bool
    {
        $token = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        if (!$token || !$chatId) {
            Log::info('ShopNotifier skipped (missing Telegram config)', $context + ['text' => $text]);
            return false;
        }

        try {
            $response = Http::timeout(8)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'disable_web_page_preview' => true,
                ]
            );

            if (!$response->successful()) {
                Log::warning('ShopNotifier Telegram failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('ShopNotifier exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
