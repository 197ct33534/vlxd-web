<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Collection;

class InvoiceImportMatcher
{
    public function normalizeDate(?string $value): ?string
    {
        $value = trim($value ?? '');
        if ($value === '') {
            return null;
        }

        $normalized = str_replace(['/', '.'], '-', $value);
        $format = preg_match('/^\d{4}-/', $normalized) ? '!Y-m-d' : '!d-m-Y';
        $date = \DateTimeImmutable::createFromFormat($format, $normalized);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
            throw new \InvalidArgumentException(__('msg.import_invalid_date', ['date' => $value]));
        }

        return $date->format('Y-m-d');
    }

    public function fingerprint(array $items, array $advances, float $total): string
    {
        $lines = array_map(fn ($item) => $this->encode([
            $this->normalizeDate($item['ngay_thang'] ?? null),
            $this->importedLine($item),
        ]), $items);
        $payments = array_map(fn ($payment) => $this->encode([
            $this->normalizeDate($payment['ngay_thang'] ?? null),
            $this->text($payment['noi_dung']),
            $this->decimal($payment['so_tien']),
        ]), $advances);
        sort($lines, SORT_STRING);
        sort($payments, SORT_STRING);

        return hash('sha256', $this->encode([$lines, $payments, $this->decimal($total)]));
    }

    public function sourceKey(string $filename, string $sheet): string
    {
        return hash('sha256', $this->encode([$this->text($filename), $this->text($sheet)]));
    }

    public function match(?Project $project, array $items, float $total, string $hash, ?string $sourceKey, ?Collection $invoices = null): array
    {
        $invoices ??= $project ? $project->invoices()->with('items')->latest('id')->get() : collect();
        if ($duplicate = $invoices->firstWhere('import_hash', $hash)) {
            return ['status' => 'duplicate', 'invoice' => $duplicate];
        }

        $imported = array_count_values(array_map(fn ($item) => $this->importedLine($item), $items));
        $legacy = [];
        $overlap = null;
        foreach ($invoices as $invoice) {
            $existing = $this->existingLines($invoice);
            if (!$invoice->import_hash && $imported && $imported == $existing
                && $this->decimal($invoice->total_amount) === $this->decimal($total)) {
                $legacy[] = $invoice;
                continue;
            }

            $shared = $this->sharedCount($imported, $existing);
            if ($shared >= 2 && $shared * 2 >= max(count($items), $invoice->items->count())
                && $shared > ($overlap['shared'] ?? 0)) {
                $overlap = ['status' => 'overlap', 'invoice' => $invoice, 'shared' => $shared];
            }
        }

        if ($sourceKey && ($previous = $invoices->firstWhere('import_source_key', $sourceKey))) {
            return [
                'status' => 'changed',
                'invoice' => $previous,
                'shared' => $this->sharedCount($imported, $this->existingLines($previous)),
            ];
        }

        if ($legacy) {
            return ['status' => 'legacy', 'invoice' => $legacy[0], 'matches' => $legacy];
        }
        if ($overlap) {
            return $overlap;
        }

        $other = Invoice::query()->where('import_hash', $hash)
            ->when($project, fn ($query) => $query->where('project_id', '!=', $project->id))
            ->with('project')->first();
        if ($other) {
            return ['status' => 'other_project', 'invoice' => $other];
        }

        return ['status' => 'ready'];
    }

    private function importedLine(array $item): string
    {
        $quantity = $item['so_luong'] ?? 0;
        $price = $item['don_gia'] ?? 0;

        return $this->line(
            $item['ten_vat_lieu'] ?? $item['material_name'] ?? '',
            $item['don_vi_tinh'] ?? $item['unit'] ?? '',
            $quantity,
            $price,
            $item['thanh_tien'] ?? (float) $quantity * (float) $price
        );
    }

    private function existingLines(Invoice $invoice): array
    {
        return array_count_values($invoice->items->map(
            fn ($item) => $this->line($item->product_name, $item->unit ?? '', $item->quantity, $item->price, $item->amount)
        )->all());
    }

    private function sharedCount(array $imported, array $existing): int
    {
        $shared = 0;
        foreach ($imported as $line => $count) {
            $shared += min($count, $existing[$line] ?? 0);
        }

        return $shared;
    }

    private function line(string $product, string $unit, $quantity, $price, $amount): string
    {
        return $this->encode([
            $this->text($product), $this->text($unit),
            $this->decimal($quantity), $this->decimal($price), $this->decimal($amount),
        ]);
    }

    private function text(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value)));
    }

    private function decimal($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function encode(array $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
