<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 3 stub: gửi ZNS / OA message khi có credentials.
 * Không có ZALO_OA_ACCESS_TOKEN thì chỉ log (safe cho local).
 */
class ZaloNotifier
{
    public function notifyOrderStatus(Order $order, string $event): bool
    {
        $token = config('zalo.oa_access_token');
        $templateId = config('zalo.zns_templates.' . $event);

        $payload = [
            'event' => $event,
            'order_code' => $order->code,
            'status' => $order->status,
            'customer_phone' => $order->customer?->phone,
            'template_id' => $templateId,
        ];

        if (!$token || !$templateId) {
            Log::info('ZaloNotifier skipped (missing config)', $payload);
            return false;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(8)
                ->post(config('zalo.zns_endpoint'), [
                    'phone' => preg_replace('/\D+/', '', (string) $order->customer?->phone),
                    'template_id' => $templateId,
                    'template_data' => [
                        'order_code' => $order->code,
                        'status' => $order->statusLabel(),
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('ZaloNotifier failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('ZaloNotifier exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
