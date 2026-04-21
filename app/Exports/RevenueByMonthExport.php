<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RevenueByMonthExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    /**
     * @param  array<int, array{0: string, 1: float, 2: float}>  $rows
     */
    public function __construct(private array $rows)
    {
    }

    public function collection(): Collection
    {
        return collect($this->rows);
    }

    public function title(): string
    {
        return 'Doanh thu';
    }

    public function headings(): array
    {
        return [
            __('reports.export.revenue_col_month'),
            __('reports.export.revenue_col_invoiced'),
            __('reports.export.revenue_col_paid'),
        ];
    }

    /**
     * @param  array{0: string, 1: float, 2: float}  $row
     */
    public function map($row): array
    {
        return [
            $row[0],
            number_format($row[1], 0, ',', '.'),
            number_format($row[2], 0, ',', '.'),
        ];
    }
}
