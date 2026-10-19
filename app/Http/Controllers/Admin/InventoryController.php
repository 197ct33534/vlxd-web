<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $products = Product::query()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('name')
            ->get();
        $categories = Product::query()->distinct()->orderBy('category')->pluck('category');

        return view('admin.inventory.index', compact('products', 'categories', 'category'));
    }
}
