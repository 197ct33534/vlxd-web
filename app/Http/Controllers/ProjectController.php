<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * All projects (quick access from sidebar; invoices still per project).
     */
    public function indexAll()
    {
        $projects = Project::with('customer')->latest()->paginate(20);

        return view('projects.all', compact('projects'));
    }

    public function index(Customer $customer)
    {
        $customer = Customer::withAccountTotals()
            ->withSum('projectReceipts as account_receipts', 'amount')
            ->withSum('advanceReceipts as account_advance_receipts', 'amount')
            ->findOrFail($customer->id);
        $projects = $customer->projects()->latest()->get();
        $projectPayments = Payment::query()->whereIn('project_id', $customer->projects()->select('id'))
            ->select('id', 'project_id', 'payment_date', 'amount', 'payment_method', 'note', 'reference_code')
            ->selectRaw('0 AS is_customer_advance');
        $advances = $customer->advances()
            ->select('id')
            ->selectRaw('NULL AS project_id')
            ->addSelect('payment_date', 'amount', 'payment_method', 'note', 'reference_code')
            ->selectRaw('1 AS is_customer_advance');
        $payments = Payment::query()->fromSub($projectPayments->unionAll($advances->getQuery()), 'payments')
            ->with('project:id,name,address')
            ->latest('payment_date')
            ->orderByDesc('id')
            ->orderByDesc('is_customer_advance')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString()
            ->fragment('customer-payment-history');

        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('projects.mobile_index', compact('customer', 'projects', 'payments'));
        }

        return view('projects.index', compact('customer', 'projects', 'payments'));
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
        ]);

        $customer->projects()->create($validated);

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', __('msg.project_created'));
    }

    public function storeAdvance(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999999.99'],
            'payment_date' => ['required', 'date_format:Y-m-d'],
            'payment_method' => ['required', 'in:cash,bank_transfer'],
            'note' => ['nullable', 'string', 'max:255'],
            'advance_reference' => ['required', 'uuid'],
        ]);

        $recorded = DB::transaction(function () use ($customer, $data) {
            $customer = Customer::lockForUpdate()->findOrFail($customer->id);
            $reference = strtolower($data['advance_reference']);
            if (Payment::where('reference_code', 'customer-advance:'.$reference)->where('for_project', 'all')
                ->whereHas('project', fn ($query) => $query->where('customer_id', $customer->id))->exists()) {
                return false;
            }

            return $customer->advances()->firstOrCreate(['reference_code' => $reference], [
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
            ])->wasRecentlyCreated;
        }, 3);

        return redirect()->route('customers.projects.index', $customer)
            ->with('success', $recorded ? 'Đã ghi tiền ứng của khách hàng.' : 'Khoản ứng này đã được ghi.');
    }

    public function adjustDebt(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'debt_amount' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'expected_debt' => ['required', 'numeric'],
            'adjustment_reason' => ['required', 'string', 'max:160'],
        ]);

        $recorded = DB::transaction(function () use ($customer, $data) {
            Customer::lockForUpdate()->findOrFail($customer->id);
            $customer = Customer::withAccountTotals()->findOrFail($customer->id);
            $current = $customer->accountBalance();
            $target = round((float) $data['debt_amount'], 2);
            if ($current === $target) {
                return false;
            }
            if ($current !== round((float) $data['expected_debt'], 2)) {
                throw ValidationException::withMessages(['debt_amount' => 'Số dư khách hàng đã thay đổi. Hãy kiểm tra lại trước khi điều chỉnh.']);
            }
            $difference = round($current - $target, 2);
            if (abs($difference) > 9999999999999.99) {
                throw ValidationException::withMessages(['debt_amount' => 'Chênh lệch vượt giới hạn lưu trữ.']);
            }
            $customer->advances()->create([
                'payment_date' => today()->toDateString(),
                'amount' => $difference,
                'payment_method' => Payment::DEBT_ADJUSTMENT,
                'reference_code' => (string) Str::uuid(),
                'note' => sprintf('Số dư %s ₫ → %s ₫. %s', number_format($current, 2, ',', '.'), number_format($target, 2, ',', '.'), $data['adjustment_reason']),
            ]);

            return true;
        }, 3);

        return redirect()->route('customers.projects.index', $customer)
            ->with('success', $recorded ? 'Đã cập nhật công nợ tổng của khách hàng.' : 'Công nợ tổng đã khớp với số tiền nhập.');
    }

    public function update(Request $request, Customer $customer, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'end_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', __('msg.project_updated'));
    }

    public function destroy(Customer $customer, Project $project)
    {
        $project->delete();

        return redirect()->route('customers.projects.index', $customer->id)
            ->with('success', __('msg.project_deleted'));
    }
}
