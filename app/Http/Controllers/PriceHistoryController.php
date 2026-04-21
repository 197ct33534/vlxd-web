<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PriceHistoryController extends Controller
{
    /**
     * Thống kê giá mới nhất theo vật tư (tên + ĐVT): có biến động hay không,
     * nếu có thì so với lần giao dịch liền trước: tăng/giảm và số tiền.
     */
    public function index(Project $project)
    {
        $project->load('customer');

        $hasInvoices = $project->invoices()->exists();

        $items = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.project_id', $project->id)
            ->select(
                'invoice_items.id',
                'invoice_items.invoice_id',
                'invoice_items.product_name',
                'invoice_items.unit',
                'invoice_items.price',
                'invoices.invoice_date',
                'invoices.code as invoice_code',
            )
            ->orderBy('invoice_items.product_name')
            ->orderBy('invoice_items.unit')
            ->orderBy('invoices.invoice_date')
            ->orderBy('invoices.id')
            ->orderBy('invoice_items.id')
            ->get();

        $sep = "\x1E";

        $grouped = $items->groupBy(function ($item) use ($sep) {
            $name = trim((string) $item->product_name);
            $unit = trim((string) ($item->unit ?? ''));

            return $name.$sep.$unit;
        });

        $summaries = $this->buildSummaries($grouped);

        return view('projects.price_history', [
            'project' => $project,
            'summaries' => $summaries,
            'hasInvoices' => $hasInvoices,
            'hasLineItems' => $items->isNotEmpty(),
        ]);
    }

    /**
     * @param  Collection<string, Collection<int, object>>  $grouped
     * @return Collection<int, object>
     */
    private function buildSummaries(Collection $grouped): Collection
    {
        return $grouped->map(function (Collection $group, string $key) {
            $parts = explode("\x1E", $key, 2);
            $productName = $parts[0] ?? '';
            $unit = $parts[1] ?? '';

            $rows = $group->sortBy(function ($item) {
                return ($item->invoice_date ?? '').'-'.$item->invoice_id.'-'.$item->id;
            })->values();

            $latest = $rows->last();
            $previous = $rows->count() > 1 ? $rows->slice($rows->count() - 2, 1)->first() : null;

            $prices = $rows->pluck('price')->map(fn ($p) => round((float) $p, 4));
            $hasVariation = $prices->unique()->count() > 1;

            $latestPrice = (float) $latest->price;
            $delta = $previous !== null ? $latestPrice - (float) $previous->price : null;

            $direction = null;
            if ($previous !== null) {
                if ($delta > 0) {
                    $direction = 'up';
                } elseif ($delta < 0) {
                    $direction = 'down';
                } else {
                    $direction = 'same';
                }
            }

            return (object) [
                'product_name' => $productName,
                'unit' => $unit,
                'line_count' => $rows->count(),
                'latest_price' => $latestPrice,
                'latest_date' => $latest->invoice_date,
                'latest_invoice_id' => $latest->invoice_id,
                'latest_invoice_code' => $latest->invoice_code,
                'has_variation' => $hasVariation,
                'delta_vs_previous' => $delta,
                'direction' => $direction,
                'abs_change' => $delta !== null ? abs($delta) : null,
            ];
        })->values()->sortBy(fn ($s) => mb_strtolower($s->product_name));
    }
}
