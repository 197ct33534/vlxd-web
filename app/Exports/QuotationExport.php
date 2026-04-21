<?php

namespace App\Exports;

use App\Models\StoreInfo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuotationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithEvents, WithTitle
{
    protected $prices;

    protected ?StoreInfo $store;

    private int $stt = 0;

    public function __construct($prices, ?StoreInfo $store = null)
    {
        $this->prices = $prices;
        $this->store = $store;
    }

    public function collection()
    {
        return $this->prices;
    }

    public function title(): string
    {
        return 'Bao gia';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'STT',
            'Tên Vật Liệu',
            'Đơn Vị Tính',
            'Đơn Giá (VNĐ)',
            'Ghi Chú',
        ];
    }

    public function map($item): array
    {
        $this->stt++;

        return [
            $this->stt,
            $item->name,
            $item->unit,
            number_format($item->price, 0, ',', '.'),
            'Cập nhật ngày '.date('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']],
            'A:E' => ['alignment' => ['vertical' => 'center']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');
                $sheet->mergeCells('A5:E5');

                $title = $this->store?->name ?? 'BÁO GIÁ VẬT TƯ';
                $sheet->setCellValue('A1', $title);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $line2 = $this->store?->address ? 'Địa chỉ: '.$this->store->address : '';
                $sheet->setCellValue('A2', $line2);

                $line3 = $this->store?->phone ? 'Điện thoại: '.$this->store->phone : '';
                $sheet->setCellValue('A3', $line3);

                $bankParts = array_filter([
                    $this->store?->bank_name ? 'NH: '.$this->store->bank_name : null,
                    $this->store?->bank_account ? 'STK: '.$this->store->bank_account : null,
                    $this->store?->bank_owner ? 'Chủ TK: '.$this->store->bank_owner : null,
                ]);
                $sheet->setCellValue('A4', implode(' | ', $bankParts));

                $sheet->setCellValue('A5', $this->store?->note ?? '');

                foreach (['A2', 'A3', 'A4', 'A5'] as $cell) {
                    $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                }
            },
        ];
    }
}
