<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentAllocation;
use App\Models\Project;
use Carbon\Carbon;

class CreditCheckService
{
    public function checkCreditStatus(Project $project): array
    {
        $project->loadMissing('customer');
        $customer = $project->customer;

        $limit = $project->credit_limit !== null
            ? (float) $project->credit_limit
            : (float) ($customer->credit_limit ?? 0);
        $days = $project->credit_days !== null
            ? (int) $project->credit_days
            : (int) ($customer->credit_days ?? 0);

        $debt = round((float) $project->total_debt, 2);
        $reasons = [];
        $overdue = [];

        if ($limit > 0 && $debt > $limit) {
            $reasons[] = 'Nợ ' . number_format($debt, 0, ',', '.') . ' vượt hạn mức ' . number_format($limit, 0, ',', '.');
        }

        if ($days > 0) {
            $invoices = Invoice::query()
                ->where('project_id', $project->id)
                ->orderBy('invoice_date')
                ->get(['id', 'code', 'invoice_date', 'total_amount']);

            foreach ($invoices as $invoice) {
                $paid = round((float) PaymentAllocation::where('invoice_id', $invoice->id)->sum('amount'), 2);
                $open = round((float) $invoice->total_amount - $paid, 2);
                if ($open <= 0 || !$invoice->invoice_date) {
                    continue;
                }
                $due = Carbon::parse($invoice->invoice_date)->addDays($days);
                if ($due->lt(now()->startOfDay())) {
                    $overdue[] = $invoice->code ?: ('#' . $invoice->id);
                }
            }

            if ($overdue) {
                $reasons[] = 'Hóa đơn quá hạn: ' . implode(', ', $overdue);
            }
        }

        return [
            'is_exceeded' => $reasons !== [],
            'reason' => implode('. ', $reasons),
            'details' => [
                'debt' => $debt,
                'limit' => $limit,
                'credit_days' => $days,
                'overdue' => $overdue,
            ],
        ];
    }
}
