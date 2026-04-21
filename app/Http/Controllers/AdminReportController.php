<?php

namespace App\Http\Controllers;

use App\Exports\DebtByProjectExport;
use App\Exports\RevenueByMonthExport;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\StoreInfo;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminReportController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('customer')
            ->orderBy('customer_id')
            ->orderBy('name')
            ->get();

        $chart = $this->buildLast12MonthsChart();

        return view('admin.reports.index', [
            'projects' => $projects,
            'chartLabels' => $chart['labels'],
            'invoiceTotals' => $chart['invoice_totals'],
            'paymentTotals' => $chart['payment_totals'],
            'totals' => [
                'invoice' => (float) Invoice::whereNotNull('invoice_date')->sum('total_amount'),
                'payments' => (float) Payment::sum('amount'),
                'debt' => (float) Project::sum('total_debt'),
            ],
        ]);
    }

    public function exportDebtExcel(): BinaryFileResponse
    {
        $rows = Project::with('customer')
            ->orderBy('customer_id')
            ->orderBy('name')
            ->get()
            ->map(fn (Project $p) => [
                $p->customer->name ?? '—',
                $p->name,
                (float) $p->total_invoice,
                (float) $p->total_paid,
                (float) $p->total_debt,
                $p->address ?? '',
            ]);

        $name = 'cong-no-cong-trinh-'.date('Y-m-d').'.xlsx';

        return Excel::download(new DebtByProjectExport($rows), $name);
    }

    public function exportRevenueExcel(): BinaryFileResponse
    {
        $chart = $this->buildLast12MonthsChart();
        $rows = [];
        foreach ($chart['labels'] as $i => $label) {
            $rows[] = [
                $label,
                $chart['invoice_totals'][$i],
                $chart['payment_totals'][$i],
            ];
        }

        $name = 'doanh-thu-theo-thang-'.date('Y-m-d').'.xlsx';

        return Excel::download(new RevenueByMonthExport($rows), $name);
    }

    public function exportSummaryPdf(): Response
    {
        $projects = Project::with('customer')
            ->orderBy('customer_id')
            ->orderBy('name')
            ->get();

        $chart = $this->buildLast12MonthsChart();
        $storeInfo = StoreInfo::first();

        $html = view('admin.reports.pdf', [
            'projects' => $projects,
            'chartLabels' => $chart['labels'],
            'invoiceTotals' => $chart['invoice_totals'],
            'paymentTotals' => $chart['payment_totals'],
            'totals' => [
                'invoice' => (float) Invoice::whereNotNull('invoice_date')->sum('total_amount'),
                'payments' => (float) Payment::sum('amount'),
                'debt' => (float) Project::sum('total_debt'),
            ],
            'storeInfo' => $storeInfo,
            'generatedAt' => Carbon::now(),
        ])->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'bao-cao-tong-hop-'.date('Y-m-d').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
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
