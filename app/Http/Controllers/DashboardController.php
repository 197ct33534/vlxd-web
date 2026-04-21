<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalInvoiceAmount = (float) Invoice::whereNotNull('invoice_date')->sum('total_amount');
        $totalPayments = (float) Payment::sum('amount');
        $totalDebt = (float) Project::sum('total_debt');

        $chart = $this->buildLast12MonthsChart();

        return view('dashboard', [
            'totalInvoiceAmount' => $totalInvoiceAmount,
            'totalPayments' => $totalPayments,
            'totalDebt' => $totalDebt,
            'chartLabels' => $chart['labels'],
            'invoiceTotals' => $chart['invoice_totals'],
            'paymentTotals' => $chart['payment_totals'],
        ]);
    }

    /**
     * @return array{labels: array<int, string>, invoice_totals: array<int, float>, payment_totals: array<int, float>}
     */
    private function buildLast12MonthsChart(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();

        $labels = [];
        $keys = [];
        for ($i = 0; $i < 12; $i++) {
            $d = $start->copy()->addMonths($i);
            $keys[] = $d->format('Y-m');
            $labels[] = $d->format('m/Y');
        }

        $invoiceTotals = array_fill_keys($keys, 0.0);
        $paymentTotals = array_fill_keys($keys, 0.0);

        $invoiceRows = Invoice::query()
            ->whereNotNull('invoice_date')
            ->where('invoice_date', '>=', $start->toDateString())
            ->get(['invoice_date', 'total_amount']);

        foreach ($invoiceRows as $row) {
            $ym = Carbon::parse($row->invoice_date)->format('Y-m');
            if (isset($invoiceTotals[$ym])) {
                $invoiceTotals[$ym] += (float) $row->total_amount;
            }
        }

        $paymentRows = Payment::query()
            ->whereNotNull('payment_date')
            ->where('payment_date', '>=', $start->toDateString())
            ->get(['payment_date', 'amount']);

        foreach ($paymentRows as $row) {
            $ym = Carbon::parse($row->payment_date)->format('Y-m');
            if (isset($paymentTotals[$ym])) {
                $paymentTotals[$ym] += (float) $row->amount;
            }
        }

        return [
            'labels' => $labels,
            'invoice_totals' => array_values($invoiceTotals),
            'payment_totals' => array_values($paymentTotals),
        ];
    }
}
