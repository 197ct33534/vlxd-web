<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\StoreInfo;
use App\Services\PaymentAllocationService;
use App\Services\ProjectDebtService;
use App\Support\VietnameseCurrency;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function __construct(private ProjectDebtService $projectDebt)
    {
    }

    /**
     * Display a listing of the invoices for a specific project.
     */
    public function index(Project $project)
    {
        $invoices = $project->invoices()
            ->withSum('paymentAllocations', 'amount')
            ->latest('invoice_date')
            ->orderByDesc('id')
            ->paginate(15);
        // Fetch payments ordered by date descending
        $payments = $project->payments()
            ->with(['paymentAllocations' => fn ($query) => $query->orderBy('id')])
            ->latest('payment_date')->get();

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
        $storeInfo = StoreInfo::current();

        // Modal xem nhanh trên danh sách: fetch gửi X-Requested-With; thêm ?modal=1 để chắc chắn trả partial
        if ($request->ajax() || $request->boolean('modal')) {
            return view('invoices.partials.details', compact('invoice', 'storeInfo'));
        }

        $projects = $invoice->project->customer->projects()->whereKeyNot($invoice->project_id)->orderBy('name')->get();
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            $invoice->loadSum('paymentAllocations', 'amount');
            return view('invoices.mobile_show', compact('invoice', 'projects', 'storeInfo'));
        }
        
        return view('invoices.show', compact('invoice', 'projects', 'storeInfo'));
    }

    public function moveToProject(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'required_without:project_name', 'prohibits:project_name'],
            'project_name' => ['nullable', 'string', 'max:255', 'required_without:project_id'],
        ]);

        DB::transaction(function () use ($invoice, $data) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $source = $invoice->project;
            if ($invoice->paymentAllocations()->exists() || DB::table('orders')->where('invoice_id', $invoice->id)->exists()) {
                throw ValidationException::withMessages(['project_id' => 'Hóa đơn đã có khoản thu hoặc đơn hàng liên kết. Cần xử lý liên kết trước khi chuyển.']);
            }

            $customer = $source->customer()->lockForUpdate()->firstOrFail();
            if (isset($data['project_id'])) {
                $destination = $customer->projects()->whereKeyNot($source->id)->find($data['project_id']);
                if (!$destination) {
                    throw ValidationException::withMessages(['project_id' => 'Hãy chọn công trình khác thuộc cùng khách hàng.']);
                }
            } else {
                if ($customer->projects()->where('name', $data['project_name'])->exists()) {
                    throw ValidationException::withMessages(['project_name' => 'Công trình này đã tồn tại. Hãy chọn trong danh sách.']);
                }
                $destination = $customer->projects()->create(['name' => $data['project_name']]);
            }

            Project::whereKey([$source->id, $destination->id])->orderBy('id')->lockForUpdate()->get();
            if ($invoice->import_hash && $destination->invoices()->where('import_hash', $invoice->import_hash)->exists()) {
                throw ValidationException::withMessages(['project_id' => 'Công trình đích đã có hóa đơn trùng nội dung nhập Excel.']);
            }
            $invoice->project()->associate($destination)->save();
            $this->projectDebt->recalculate($source->id);
            $this->projectDebt->recalculate($destination->id);
        }, 3);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Đã chuyển hóa đơn sang công trình mới.');
    }

    /**
     * Download invoice as PDF (DomPDF).
     */
    public function pdf(Invoice $invoice)
    {
        $invoice->load(['items', 'project.customer']);
        $storeInfo = StoreInfo::first();
        $amountInWords = VietnameseCurrency::toWords($invoice->total_amount);

        $html = view('invoices.pdf', compact('invoice', 'storeInfo', 'amountInWords'))->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $name = 'hoadon-'.($invoice->code ?: $invoice->id).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    /**
     * Store a manual payment for the project.
     */
    public function storePayment(Request $request, Project $project, PaymentAllocationService $allocations)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer',
            'reference_code' => 'nullable|string|max:100',
            'proof_image' => 'nullable|image|max:5120',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $project, $allocations) {
            $proof = $request->hasFile('proof_image')
                ? $request->file('proof_image')->store('payments', 'public')
                : null;

            $payment = Payment::create([
                'project_id' => $project->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'note' => $request->note,
                'payment_type' => Payment::normalizeType('tung_dot'),
                'for_project' => 'project',
                'payment_method' => $request->input('payment_method', 'cash'),
                'reference_code' => $request->input('reference_code'),
                'proof_image' => $proof,
            ]);

            $allocations->allocate($payment, $request->filled('invoice_id') ? (int) $request->invoice_id : null);
        });

        return redirect()->back()->with('success', __('msg.payment_recorded'));
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
            'invoice_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.date' => 'nullable|date_format:Y-m-d',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $project) {
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['price'];
                $totalAmount += $lineTotal;
                $itemsData[] = [
                    'product_name' => $item['product_name'],
                    'date' => $item['date'] ?? null,
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

            $this->projectDebt->recalculate($project->id);
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', __('msg.invoice_created'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'project']);
        $editItems = $invoice->items->map(fn ($item) => [
            ...$item->only(['product_name', 'unit', 'quantity', 'price']),
            'date' => $item->deliveryDate('Y-m-d'),
        ]);
        
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('invoices.mobile_edit', compact('invoice', 'editItems'));
        }

        return view('invoices.edit', compact('invoice', 'editItems'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.date' => 'nullable|date_format:Y-m-d',
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
                    'date' => $item['date'] ?? null,
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

            $this->projectDebt->recalculate($project->id);
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', __('msg.invoice_updated'));
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

            $this->projectDebt->recalculate($project->id);
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', __('msg.invoice_deleted'));
    }

    /**
     * Update the specified payment.
     */
    public function updatePayment(Request $request, \App\Models\Payment $payment, PaymentAllocationService $allocations)
    {
        abort_if($payment->isDebtAdjustment(), 403);

        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer',
            'reference_code' => 'nullable|string|max:100',
            'proof_image' => 'nullable|image|max:5120',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $project = $payment->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $payment, $allocations) {
            $payload = [
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'note' => $request->note,
                'payment_method' => $request->input('payment_method', $payment->payment_method ?: 'cash'),
                'reference_code' => $request->input('reference_code'),
            ];
            if ($request->hasFile('proof_image')) {
                $payload['proof_image'] = $request->file('proof_image')->store('payments', 'public');
            }
            $payment->update($payload);
            $allocations->allocate($payment, $request->filled('invoice_id') ? (int) $request->invoice_id : null);
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', __('msg.payment_updated'));
    }

    /**
     * Remove the specified payment.
     */
    public function destroyPayment(\App\Models\Payment $payment)
    {
        abort_if($payment->isDebtAdjustment(), 403);

        $project = $payment->project;

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $project) {
            // Delete Payment
            $payment->delete();

            $this->projectDebt->recalculate($project->id);
        });

        return redirect()->route('projects.invoices.index', $project->id)->with('success', __('msg.payment_deleted'));
    }

    public function adjustDebt(Request $request, Project $project)
    {
        $data = $request->validate([
            'debt_amount' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'expected_debt' => ['required', 'numeric', 'min:0'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
        ]);

        $recorded = DB::transaction(function () use ($project, $data) {
            $project = Project::lockForUpdate()->findOrFail($project->id);
            $this->projectDebt->recalculate($project->id);
            $project->refresh();
            $current = (float) $project->total_debt;
            $target = round((float) $data['debt_amount'], 2);

            if ($current === $target) {
                return false;
            }
            if ($current !== round((float) $data['expected_debt'], 2)) {
                throw ValidationException::withMessages(['debt_amount' => 'Công nợ đã thay đổi. Hãy kiểm tra số dư mới rồi thử lại.']);
            }

            $amount = round((float) $project->total_invoice - (float) $project->total_paid - $target, 2);
            if (abs($amount) > 9999999999999.99) {
                throw ValidationException::withMessages(['debt_amount' => 'Chênh lệch vượt giới hạn lưu trữ.']);
            }
            $project->payments()->create([
                'payment_date' => now(),
                'amount' => $amount,
                'payment_method' => Payment::DEBT_ADJUSTMENT,
                'payment_type' => Payment::normalizeType('tung_dot'),
                'for_project' => 'project',
                'note' => sprintf('Điều chỉnh công nợ: %s ₫ → %s ₫. %s',
                    rtrim(rtrim(number_format($current, 2, ',', '.'), '0'), ','),
                    rtrim(rtrim(number_format($target, 2, ',', '.'), '0'), ','),
                    $data['adjustment_reason']
                ),
            ]);
            $this->projectDebt->recalculate($project->id);

            return true;
        }, 3);

        return redirect()->route('projects.invoices.index', $project)
            ->with('success', $recorded ? 'Đã ghi phiếu điều chỉnh công nợ.' : 'Công nợ đã khớp với số tiền nhập.');
    }

    /**
     * Mark the invoice as fully paid.
     */
    public function markAsPaid(Invoice $invoice)
    {
        $project = $invoice->project;

        $recorded = DB::transaction(function () use ($invoice, $project) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $amount = $invoice->outstandingAmount();
            if ($amount <= 0) {
                return false;
            }

            $payment = $project->payments()->create([
                'payment_date' => now(),
                'amount' => $amount,
                'note' => 'Payment for invoice #' . ($invoice->code ?? $invoice->id),
                'payment_type' => Payment::normalizeType('tung_dot'),
                'payment_method' => 'cash',
            ]);

            $invoice->paymentAllocations()->create(['payment_id' => $payment->id, 'amount' => $amount]);
            $this->projectDebt->recalculate($project->id);

            return true;
        }, 3);

        return redirect()->route('projects.invoices.index', $project->id)
            ->with('success', $recorded ? __('msg.invoice_marked_paid') : 'Hóa đơn này đã thu đủ.');
    }
}
