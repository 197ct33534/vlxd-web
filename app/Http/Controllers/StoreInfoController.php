<?php

namespace App\Http\Controllers;

use App\Models\StoreInfo;
use Illuminate\Http\Request;

class StoreInfoController extends Controller
{
    public function edit()
    {
        $storeInfo = StoreInfo::getOrCreateDefault();

        return view('store_settings.edit', compact('storeInfo'));
    }

    public function update(Request $request)
    {
        $storeInfo = StoreInfo::getOrCreateDefault();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bank_owner' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $storeInfo->update($validated);

        return redirect()->route('store-settings.edit')->with('success', __('msg.store_updated'));
    }
}
