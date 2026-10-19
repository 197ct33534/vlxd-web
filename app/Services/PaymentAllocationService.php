<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    public function allocate(Payment $payment, ?int $specificInvoiceId = null): void
    {
        DB::transaction(function () use ($payment, $specificInvoiceId) {
            $payment->paymentAllocations()->delete();

            $remaining = round((float) $payment->amount, 2);
            if ($remaining <= 0 || !$payment->project_id) {
                app(ProjectDebtService::class)->recalculate($payment->project_id);
                return;
            }

            $invoices = Invoice::query()
                ->where('project_id', $payment->project_id)
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $queue = collect();
            if ($specificInvoiceId) {
                $chosen = $invoices->firstWhere('id', $specificInvoiceId);
                if ($chosen) {
                    $queue->push($chosen);
                }
            }
            foreach ($invoices as $invoice) {
                if ($specificInvoiceId && $invoice->id === $specificInvoiceId) {
                    continue;
                }
                $queue->push($invoice);
            }

            foreach ($queue as $invoice) {
                if ($remaining <= 0) {
                    break;
                }
                $paid = round((float) PaymentAllocation::where('invoice_id', $invoice->id)->sum('amount'), 2);
                $open = round((float) $invoice->total_amount - $paid, 2);
                if ($open <= 0) {
                    continue;
                }
                $apply = round(min($remaining, $open), 2);
                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $apply,
                ]);
                $remaining = round($remaining - $apply, 2);
            }

            app(ProjectDebtService::class)->recalculate($payment->project_id);
        });
    }
}
