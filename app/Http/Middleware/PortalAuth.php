<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $customerId = $request->session()->get('portal_customer_id');

        if (!$customerId) {
            return redirect()->route('portal.login');
        }

        $customer = Customer::find($customerId);

        if (!$customer) {
            $request->session()->forget('portal_customer_id');
            return redirect()->route('portal.login');
        }

        $request->attributes->set('portalCustomer', $customer);
        view()->share('portalCustomer', $customer);

        return $next($request);
    }
}
