<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportItem;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    /**
     * Display reports. Admin sees all, User sees own.
     */
    public function index()
    {
        $query = DailyReport::with('user', 'items.project')->latest();

        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $reports = $query->get();

        return view('daily_reports.index', compact('reports'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $projects = Project::all();
        return view('daily_reports.create', compact('projects'));
    }

    /**
     * Store report + items.
     */
    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.project_id' => 'required|exists:projects,id',
            'items.*.product_name' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $report = DailyReport::create([
                'user_id' => Auth::id(),
                'report_date' => $request->report_date,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $report->items()->create($item);
            }
        });

        return redirect()->route('daily-reports.index')->with('success', 'Report submitted successfully.');
    }

    /**
     * Show report details.
     */
    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load('items.project', 'user');
        return view('daily_reports.show', compact('dailyReport'));
    }

    /**
     * Approve report.
     */
    public function approve(DailyReport $dailyReport)
    {
        $dailyReport->update(['status' => 'approved']);
        return back()->with('success', 'Report approved.');
    }

    /**
     * Reject report.
     */
    public function reject(Request $request, DailyReport $dailyReport)
    {
        $dailyReport->update([
            'status' => 'rejected', 
            'admin_note' => $request->admin_note
        ]);
        return back()->with('success', 'Report rejected.');
    }

    /**
     * API: Get latest price for a product in a project.
     */
    public function getLatestPrice(Request $request)
    {
        $projectId = $request->query('project_id');
        $productName = $request->query('product_name');

        if (!$projectId || !$productName) {
            return response()->json(['price' => null]);
        }

        // Find the latest invoice item for this product in this project
        $lastItem = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.project_id', $projectId)
            ->where('invoice_items.product_name', 'LIKE', $productName) // Loose match might be better, or strict? Using LIKE for now.
            ->orderBy('invoices.invoice_date', 'desc')
            ->select('invoice_items.price')
            ->first();

        return response()->json([
            'price' => $lastItem ? (float)$lastItem->price : null
        ]);
    }
}
