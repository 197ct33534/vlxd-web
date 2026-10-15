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

        $totalDebt = $projects->sum(fn ($p) => (float) $p->total_debt);

        $recentOrders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.home', compact('customer', 'projects', 'totalDebt', 'recentOrders'));
    }
}
