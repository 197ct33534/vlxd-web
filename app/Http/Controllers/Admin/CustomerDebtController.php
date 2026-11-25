<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\StoreInfo;
use App\Services\CreditCheckService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class CustomerDebtController extends Controller
{
    public function index(CreditCheckService $credit)
    {
        $customers = Customer::withAccountTotals()->with('projects')->orderBy('name')->get()->map(function (Customer $customer) use ($credit) {
            $debt = max(0, $customer->accountBalance());
            $exceeded = false;
            $projects = $customer->projects->map(function (Project $project) use ($credit, &$exceeded) {
                $status = $credit->checkCreditStatus($project);
                $exceeded = $exceeded || $status['is_exceeded'];

                return ['project' => $project, 'credit' => $status];
            });

            return [
                'customer' => $customer,
                'debt' => $debt,
                'exceeded' => $exceeded,
                'projects' => $projects,
            ];
        });

        return view('admin.customers.debt_summary', compact('customers'));
    }

    public function updateCustomerCredit(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'credit_days' => 'required|integer|min:0',
        ]);
        $customer->update($data);

        return back()->with('success', 'Đã lưu hạn mức khách ' . $customer->name);
    }

    public function updateProjectCredit(Request $request, Project $project)
    {
        $data = $request->validate([
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0',
        ]);
        $project->update([
            'credit_limit' => $request->filled('credit_limit') ? $data['credit_limit'] : null,
            'credit_days' => $request->filled('credit_days') ? $data['credit_days'] : null,
        ]);

        return back()->with('success', 'Đã lưu hạn mức công trình ' . $project->name);
    }

    public function reconciliationPdf(Request $request, Project $project)
    {
        $data = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $from = $data['from_date'] ?? '2000-01-01';
        $to = $data['to_date'] ?? now()->toDateString();
        $project->load('customer');

        $openingInvoice = (float) Invoice::where('project_id', $project->id)->whereDate('invoice_date', '<', $from)->sum('total_amount');
        $openingPaid = (float) Payment::where('project_id', $project->id)->whereDate('payment_date', '<', $from)->sum('amount');
        $opening = round($openingInvoice - $openingPaid, 2);

        $invoices = Invoice::where('project_id', $project->id)
            ->whereDate('invoice_date', '>=', $from)
            ->whereDate('invoice_date', '<=', $to)
            ->orderBy('invoice_date')
            ->get();
        $payments = Payment::where('project_id', $project->id)
            ->whereDate('payment_date', '>=', $from)
            ->whereDate('payment_date', '<=', $to)
            ->orderBy('payment_date')
            ->get();

        $periodInvoice = round((float) $invoices->sum('total_amount'), 2);
        $periodPaid = round((float) $payments->sum('amount'), 2);
        $closing = round($opening + $periodInvoice - $periodPaid, 2);

        $laterInvoice = Invoice::where('project_id', $project->id)->whereDate('invoice_date', '>', $to)->exists();
        $laterPayment = Payment::where('project_id', $project->id)->whereDate('payment_date', '>', $to)->exists();
        $coversBook = !$laterInvoice && !$laterPayment;
        if ($coversBook) {
            $closing = round((float) $project->total_debt, 2);
        }

        $store = StoreInfo::first();
        $html = view('pdf.debt_reconciliation', compact(
            'project', 'store', 'from', 'to', 'opening', 'invoices', 'payments', 'periodInvoice', 'periodPaid', 'closing', 'coversBook'
        ))->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="doi-soat-' . $project->id . '.pdf"',
        ]);
    }
}
