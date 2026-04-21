<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\StoreInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices for a specific project.
     */
    public function index(Project $project)
    {
        $invoices = $project->invoices()->latest('invoice_date')->paginate(15);
        // Fetch payments ordered by date descending
        $payments = $project->payments()->latest('payment_date')->get();

        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('invoices.mobile_index', compact('project', 'invoices', 'payments'));
        }

        return view('invoices.index', compact('project', 'invoices', 'payments'));
    }

    /**
     * Display the specified invoice details.
     */
    public function show(Request $request, Invoice $invoice)
    {
        // Load relationships if necessary
        $invoice->load(['items', 'project.customer']);
        
        if ($request->ajax()) {
            return view('invoices.partials.details', compact('invoice'));
        }

        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('invoices.mobile_show', compact('invoice'));
        }
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF (DomPDF).
     */
    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'project.customer']);
        $storeInfo = StoreInfo::first();

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'storeInfo'))
            ->setPaper('a4', 'portrait');

        $name = 'hoadon-'.($invoice->code ?: $invoice->id).'.pdf';

        return $pdf->stream($name);
    }

    /**
     * Store a manual payment for the project.
     */
    public function storePayment(Request $request, Project $project)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $project) {
            // Create Payment
            \App\Models\Payment::create([
                'project_id' => $project->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'note' => $request->note,
                'payment_type' => 'manual', // or 'tien_mat', 'chuyen_khoan'
                'for_project' => 'project',
            ]);

            // Update Project totals
            $totalPaid = \App\Models\Payment::where('project_id', $project->id)->sum('amount');
            $project->total_paid = $totalPaid;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Project $project)
    {
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('invoices.mobile_create', compact('project'));
        }

        return view('invoices.create', compact('project'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $project) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['price'];
                $totalAmount += $lineTotal;
                $itemsData[] = [
                    'product_name' => $item['product_name'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $lineTotal,
                ];
            }

            // Create Invoice
            $invoice = \App\Models\Invoice::create([
                'project_id' => $project->id,
                'code' => $request->code,
                'invoice_date' => $request->invoice_date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // Create Items
            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            // Update Project Totals
            $totalInvoices = \App\Models\Invoice::where('project_id', $project->id)->sum('total_amount');
            $project->total_invoice = $totalInvoices;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Invoice created successfully.');
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'project']);
        
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('invoices.mobile_edit', compact('invoice'));
        }

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $project = $invoice->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $invoice, $project) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['price'];
                $totalAmount += $lineTotal;
                $itemsData[] = [
                    'product_name' => $item['product_name'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $lineTotal,
                ];
            }

            // Update Invoice
            $invoice->update([
                'code' => $request->code,
                'invoice_date' => $request->invoice_date,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // Delete old items and create new ones
            $invoice->items()->delete();
            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            // Update Project Totals
            $totalInvoices = \App\Models\Invoice::where('project_id', $project->id)->sum('total_amount');
            $project->total_invoice = $totalInvoices;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $project = $invoice->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($invoice, $project) {
            // Delete invoice (cascade deletes items set in migration usually, but we rely on simple deletion here or manual if needed)
            // Ideally InvoiceItems should have onDelete('cascade')
            $invoice->items()->delete(); // Safe manual delete
            $invoice->delete();

            // Update Project Totals
            $totalInvoices = \App\Models\Invoice::where('project_id', $project->id)->sum('total_amount');
            $project->total_invoice = $totalInvoices;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Update the specified payment.
     */
    public function updatePayment(Request $request, \App\Models\Payment $payment)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $project = $payment->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $payment, $project) {
            // Update Payment
            $payment->update([
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            // Update Project Totals
            $totalPaid = $project->payments()->sum('amount');
            $project->total_paid = $totalPaid;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment.
     */
    public function destroyPayment(\App\Models\Payment $payment)
    {
        $project = $payment->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $project) {
            // Delete Payment
            $payment->delete();

            // Update Project Totals
            $totalPaid = $project->payments()->sum('amount');
            $project->total_paid = $totalPaid;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Payment deleted successfully.');
    }

    /**
     * Mark the invoice as fully paid.
     */
    public function markAsPaid(Invoice $invoice)
    {
        $project = $invoice->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($invoice, $project) {
            // Create Payment
            $project->payments()->create([
                'payment_date' => now(),
                'amount' => $invoice->total_amount,
                'note' => 'Payment for invoice #' . ($invoice->code ?? $invoice->id),
                'payment_type' => 'manual',
            ]);

            // Update Project Totals
            $totalPaid = $project->payments()->sum('amount');
            $project->total_paid = $totalPaid;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', 'Invoice marked as paid.');
    }
}
