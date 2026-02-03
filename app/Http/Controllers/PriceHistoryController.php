<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceHistoryController extends Controller
{
    /**
     * Display the price history for products in the project.
     */
    public function index(Project $project)
    {
        // Fetch all invoice items for this project, joined with invoices
        // Grouped by product_name in the view logic, or ordered here.
        // We really want a history per product.
        
        $items = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.project_id', $project->id)
            ->select(
                'invoice_items.*', 
                'invoices.invoice_date', 
                'invoices.code as invoice_code',
                'invoices.id as invoice_id'
            )
            ->orderBy('invoice_items.product_name')
            ->orderBy('invoices.invoice_date')
            ->get();

        // Group by composite key: Name | Unit | Quantity
        $history = $items->groupBy(function ($item) {
            return $item->product_name . '|' . $item->unit . '|' . (float)$item->quantity;
        })->filter(function ($group) {
            // Only keep groups where the price has changed at least once
            return $group->pluck('price')->unique()->count() > 1;
        });

        return view('projects.price_history', compact('project', 'history'));
    }
}
