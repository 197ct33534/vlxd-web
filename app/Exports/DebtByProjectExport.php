<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DebtByProjectExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Cong no';
    }

    public function headings(): array
    {
        return [
            __('reports.export.debt_col_customer'),
            __('reports.export.debt_col_project'),
            __('reports.export.debt_col_invoiced'),
            __('reports.export.debt_col_paid'),
            __('reports.export.debt_col_debt'),
            __('reports.export.debt_col_address'),
        ];
    }

    /**
     * @param  array{0: string, 1: string, 2: float, 3: float, 4: float, 5: string}  $row
     */
    public function map($row): array
    {
        return [
            $row[0],
            $row[1],
            number_format($row[2], 0, ',', '.'),
            number_format($row[3], 0, ',', '.'),
            number_format($row[4], 0, ',', '.'),
            $row[5],
        ];
    }
}
