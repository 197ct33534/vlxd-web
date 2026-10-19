<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockReceiptController extends Controller
{
    public function index()
    {
        $receipts = StockReceipt::with('supplier')->latest('receipt_date')->paginate(30);

        return view('admin.stock_receipts.index', compact('receipts'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.stock_receipts.create', compact('suppliers', 'products'));
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_name' => 'nullable|string|max:255',
            'receipt_date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'nullable|string|max:255',
            'items.*.category' => 'nullable|string|max:50',
            'items.*.base_unit' => 'nullable|string|max:50',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $data['items'] = collect($data['items'])->map(function (array $row) {
            if (empty($row['product_id']) && !empty($row['product_name'])) {
                $row['product_id'] = Product::create([
                    'name' => $row['product_name'],
                    'category' => $row['category'] ?: 'khac',
                    'base_unit' => $row['base_unit'] ?: 'cai',
                    'retail_price' => 0,
                    'is_active' => true,
                ])->id;
            }
            return $row;
        })->filter(fn ($row) => !empty($row['product_id']))->values()->all();

        $supplierId = $data['supplier_id'] ?? null;
        if (!$supplierId && !empty($data['supplier_name'])) {
            $supplierId = Supplier::create(['name' => $data['supplier_name']])->id;
        }
        if (!$supplierId) {
            return back()->withInput()->with('error', 'Chọn hoặc nhập tên nhà cung cấp.');
        }

        $receipt = $stock->importStock([
            'supplier_id' => $supplierId,
            'receipt_date' => $data['receipt_date'],
            'note' => $data['note'] ?? null,
        ], $data['items']);

        if ($receipt->items->isEmpty()) {
            $receipt->delete();
            return back()->withInput()->with('error', 'Chưa có dòng nhập có số lượng.');
        }

        return redirect()->route('admin.stock-receipts.index')
            ->with('success', 'Đã nhập kho ' . $receipt->code);
    }
}
