<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LandingQuoteRequestController extends Controller
{
    /**
     * Khách để lại SĐT trên landing — gửi tin nhắn vào nhóm Telegram cho nhân viên gọi lại.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ], [
            'phone.required' => __('landing.quote_phone_required'),
        ]);

        $digits = preg_replace('/\D/', '', $validated['phone']);
        if (strlen($digits) < 9 || strlen($digits) > 11) {
            return back()->withErrors(['phone' => __('landing.quote_phone_invalid')])->withInput();
        }

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::warning('landing.quote_request: Telegram not configured');

            return back()->withErrors(['phone' => __('landing.quote_not_configured')])->withInput();
        }

        $text = $this->buildTelegramText($request, $validated['phone'], $digits);

        $response = Http::timeout(15)->asForm()->post(
            "https://api.telegram.org/bot{$token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]
        );

        if (! $response->successful()) {
            Log::error('Telegram sendMessage failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return back()->withErrors(['phone' => __('landing.quote_send_failed')])->withInput();
        }

        return back()->with('quote_request_success', true);
    }

    private function buildTelegramText(Request $request, string $rawPhone, string $digits): string
    {
        $safeRaw = htmlspecialchars($rawPhone, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeDigits = htmlspecialchars($digits, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $ip = htmlspecialchars((string) ($request->ip() ?? '—'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $time = now()->format('d/m/Y H:i:s');

        return "<b>📞 Yêu cầu báo giá (website)</b>\n"
            ."SĐT nhập: {$safeRaw}\n"
            ."Chuẩn hoá: <code>{$safeDigits}</code>\n"
            ."⏰ {$time}\n"
            ."🌐 IP: {$ip}";
    }
}
