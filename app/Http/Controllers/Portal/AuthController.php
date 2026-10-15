<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('portal_customer_id')) {
            return redirect()->route('portal.home');
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|max:30',
        ]);

        $digits = preg_replace('/\D+/', '', $data['phone']);

        $customer = Customer::query()
            ->whereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '.', '') LIKE ?", ['%' . $digits])
            ->orWhere('phone', $data['phone'])
            ->first();

        if (!$customer) {
            return back()
                ->withInput()
                ->with('error', 'Không tìm thấy khách hàng với số điện thoại này. Hãy gọi cửa hàng để được hỗ trợ.');
        }

        $request->session()->put('portal_customer_id', $customer->id);

        return redirect()->route('portal.home');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('portal_customer_id');

        return redirect()->route('portal.login');
    }
}
