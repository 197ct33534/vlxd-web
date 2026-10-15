<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInfo;
use Illuminate\Http\Request;

class StoreInfoController extends Controller
{
    public function edit()
    {
        $store = StoreInfo::query()->first() ?? new StoreInfo([
            'name' => 'Cửa hàng vật liệu xây dựng',
        ]);

        return view('admin.store_info.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'zalo_oa_id' => 'nullable|string|max:100',
            'zalo_oa_url' => 'nullable|url|max:500',
            'bank_account' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_owner' => 'nullable|string|max:150',
            'note' => 'nullable|string|max:1000',
        ]);

        $store = StoreInfo::query()->first();
        if ($store) {
            $store->update($data);
        } else {
            StoreInfo::create($data);
        }

        return back()->with('success', 'Đã cập nhật thông tin cửa hàng (Gọi / Zalo portal).');
    }
}
