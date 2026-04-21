<?php

namespace App\Http\Controllers;

use App\Models\StoreInfo;
use App\Exports\QuotationExport;
use Maatwebsite\Excel\Facades\Excel;

class QuotationController extends Controller
{
    /**
     * Get the latest prices for all materials.
     */
    public static function getLatestPrices()
    {
        return \App\Models\MaterialPrice::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Download the latest quotation as an Excel file.
     */
    public function download()
    {
        $prices = self::getLatestPrices();
        $store = StoreInfo::first();

        if ($prices->isEmpty()) {
            return back()->with('error', __('msg.quotation_empty'));
        }

        return Excel::download(
            new QuotationExport($prices, $store),
            'bao-gia-vat-lieu-' . date('d-m-Y') . '.xlsx'
        );
    }
}
