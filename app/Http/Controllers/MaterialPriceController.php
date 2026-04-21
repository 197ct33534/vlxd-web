<?php

namespace App\Http\Controllers;

use App\Models\MaterialPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prices = MaterialPrice::orderBy('display_order')->orderBy('name')->paginate(30);
        
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('material_prices.mobile_index', compact('prices'));
        }
        
        return view('material_prices.index', compact('prices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('material_prices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
        ]);

        MaterialPrice::create($request->all());

        return redirect()->route('material-prices.index')->with('success', __('msg.material_price_created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialPrice $materialPrice)
    {
        return view('material_prices.edit', compact('materialPrice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialPrice $materialPrice)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $materialPrice->update($data);

        return redirect()->route('material-prices.index')->with('success', __('msg.material_price_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialPrice $materialPrice)
    {
        $materialPrice->delete();
        return redirect()->route('material-prices.index')->with('success', __('msg.material_price_deleted'));
    }

    /**
     * Remove multiple material prices (bulk delete from index checkboxes).
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:material_prices,id',
        ]);

        $count = MaterialPrice::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('material-prices.index')->with('success', __('msg.material_prices_bulk_deleted', ['count' => $count]));
    }

    /**
     * One-time sync from invoice items history.
     */
    public function syncFromHistory()
    {
        $latestInvoiceItems = DB::table('invoice_items')
            ->select('product_name', 'unit', 'price')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('invoice_items')
                    ->groupBy('product_name');
            })
            ->get();

        foreach ($latestInvoiceItems as $item) {
            MaterialPrice::updateOrCreate(
                ['name' => $item->product_name],
                [
                    'unit' => $item->unit,
                    'price' => $item->price,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('material-prices.index')->with('success', __('msg.material_price_synced'));
    }
}
