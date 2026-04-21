<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Jenssegers\Agent\Agent;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->get('has_projects') === 'yes') {
            $query->has('projects');
        } elseif ($request->get('has_projects') === 'no') {
            $query->doesntHave('projects');
        }

        $sort = $request->get('sort', 'newest');
        $allowedSorts = ['newest', 'oldest', 'name_asc', 'name_desc', 'debt_desc', 'projects_desc'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'newest';
        }

        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'oldest' => $query->oldest(),
            'debt_desc' => $query->orderByRaw('(
                SELECT COALESCE(SUM(p.total_debt), 0) FROM projects p WHERE p.customer_id = customers.id
            ) DESC'),
            'projects_desc' => $query->orderByRaw('(
                SELECT COUNT(*) FROM projects p WHERE p.customer_id = customers.id
            ) DESC'),
            default => $query->latest(),
        };

        $perPage = $request->integer('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $customers = $query->withCount('projects')
            ->withSum('projects as total_debt', 'total_debt')
            ->paginate($perPage)
            ->withQueryString();
            
        $agent = new Agent();
        if ($agent->isMobile()) {
            return view('customer.mobile', compact('customers'));
        }

        return view('customer.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', __('msg.customer_added'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', __('msg.customer_updated'));
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', __('msg.customer_deleted'));
    }
}
