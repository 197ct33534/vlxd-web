<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $projects = $customer->projects()
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'address', 'total_invoice', 'total_paid', 'total_debt', 'status']);

        $customer->loadSum('advances as account_advanced', 'amount');
        $customer->account_invoiced = $projects->sum('total_invoice');
        $customer->account_project_paid = $projects->sum('total_paid');
        $totalDebt = max(0, $customer->accountBalance());

        $recentOrders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.home', compact('customer', 'projects', 'totalDebt', 'recentOrders'));
    }
}
