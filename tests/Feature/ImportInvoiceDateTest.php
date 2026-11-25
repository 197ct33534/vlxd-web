<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportInvoiceDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_workbook_previews_legacy_matches_and_only_saves_new_sheets(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = Customer::create(['name' => 'Anh Hùng']);
        $origin = $customer->projects()->create(['name' => 'Công trình khác']);
        $project = $customer->projects()->create(['name' => 'Hẽm 297/7']);
        $legacy = $project->invoices()->create(['total_amount' => 200000, 'invoice_date' => '2026-05-10']);
        $legacy->items()->create([
            'product_name' => 'Đá 1x2', 'unit' => 'm3', 'quantity' => 2,
            'price' => 100000, 'amount' => 200000, 'date' => '2026-05-10',
        ]);

        $rows = [
            'Sheet cũ' => ['16/11/2026', 'Đá 1x2', 'm3', 2, 100000],
            'Sheet mới' => ['17/11/2026', 'Xi măng', 'bao', 4, 75000],
            'Sheet lỗi' => ['31/11/2026', 'Gạch', 'viên', 6, 1000],
        ];
        $upload = $this->uploadWorkbook($rows, 'anh-hung.xlsx', $origin->id);
        $preview = $this->get($upload)->assertOk()->assertSee('Công trình khác');
        $this->assertSame('ready', $preview->viewData('sheets')[0]['match']['status']);

        $url = strtok($upload, '?').'?project_id='.$project->id;
        $preview = $this->get($url)->assertOk()
            ->assertSee('Có thể đã nhập')
            ->assertSee('Ngày xuất hóa đơn')
            ->assertSee('Ngày giao cuối 17/11/2026')
            ->assertSee('Hóa đơn #'.$legacy->id)
            ->assertSee('Ngày không hợp lệ');
        $this->assertSame(['legacy', 'ready', 'invalid'], array_column(array_column($preview->viewData('sheets'), 'match'), 'status'));

        $saveUrl = strtok($url, '?');
        $this->post($saveUrl, ['sheets' => [1], 'confirmed' => 1])->assertSessionHasErrors('project_id');
        $this->post($saveUrl, ['project_id' => $project->id, 'sheets' => [1]])->assertSessionHasErrors('confirmed');

        $request = ['project_id' => $project->id, 'sheets' => [0, 1, 2], 'confirmed' => 1];
        $this->post($saveUrl, $request)->assertRedirect($url)
            ->assertSessionHas('sheet_results.0.status', 'error')
            ->assertSessionHas('sheet_results.1.status', 'saved')
            ->assertSessionHas('sheet_results.2.status', 'error');

        $this->assertDatabaseCount('invoices', 2);
        $this->assertDatabaseHas('invoices', [
            'project_id' => $project->id, 'total_amount' => 300000, 'import_sheet' => 'Sheet mới',
            'invoice_date' => '2026-11-21 00:00:00',
        ]);
        $this->assertDatabaseHas('invoice_items', ['product_name' => 'Xi măng', 'date' => '2026-11-17']);
        $this->assertDatabaseMissing('invoices', ['project_id' => $origin->id]);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'total_debt' => 500000]);

        $this->post($saveUrl, $request)->assertSessionHas('sheet_results.1.status', 'duplicate');
        $renamed = $this->uploadWorkbook(['Đổi tên' => $rows['Sheet mới']], 'renamed.xlsx', $project->id);
        $this->get($renamed)->assertOk()->assertSee('Đã nhập');
        $this->assertDatabaseCount('invoices', 2);

        $changed = $this->uploadWorkbook([
            'Sheet mới' => ['17/11/2026', 'Xi măng', 'bao', 5, 75000],
        ], 'anh-hung.xlsx', $project->id);
        $this->get($changed)->assertOk()->assertSee('Nội dung đã thay đổi')->assertSee('75.000');
        $request['sheets'] = [0];
        $this->post(strtok($changed, '?'), $request)->assertSessionHas('sheet_results.0.status', 'error');
        $this->post(strtok($changed, '?'), $request + ['reviewed' => [0]])
            ->assertSessionHas('sheet_results.0.status', 'saved');
        $this->assertDatabaseCount('invoices', 3);
    }

    public function test_workbook_creates_one_project_and_skips_identical_sheets_in_the_same_batch(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $line = ['16/11/2026', 'Đá 1x2', 'm3', 2, 100000];
        $url = $this->uploadWorkbook([
            'Sheet 1' => $line,
            'Bản sao' => $line,
            'Sheet 2' => ['17/11/2026', 'Xi măng', 'bao', 4, 75000],
        ], 'hoa-don.xlsx');

        $this->get($url)->assertOk()->assertSee('Trùng nội dung trong tệp');
        $this->post($url, ['project_name' => 'Công trình mới', 'sheets' => [0, 1, 2], 'confirmed' => 1])
            ->assertSessionHas('sheet_results.0.status', 'saved')
            ->assertSessionHas('sheet_results.1.status', 'duplicate')
            ->assertSessionHas('sheet_results.2.status', 'saved');

        $this->assertDatabaseCount('projects', 1);
        $this->assertDatabaseCount('invoices', 2);
        $this->assertDatabaseHas('projects', ['name' => 'Công trình mới', 'total_debt' => 500000]);
    }

    private function uploadWorkbook(array $rows, string $filename, ?int $projectId = null): string
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        foreach ($rows as $name => $row) {
            $sheet->setTitle($name);
            $sheet->setCellValue('A5', 'Khách hàng: Anh Hùng');
            $sheet->fromArray($row, null, 'B8');
            $sheet->setCellValue('E12', 'Ngày 21 Tháng 11 Năm2026');
            if ($name !== array_key_last($rows)) {
                $sheet = $book->createSheet();
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'invoice-test-');

        try {
            (new Xlsx($book))->save($path);
            return $this->post(route('invoice.import'), [
                'project_id' => $projectId,
                'file' => new UploadedFile($path, $filename, null, null, true),
            ])->assertRedirect()->headers->get('Location');
        } finally {
            unlink($path);
        }
    }

    public function test_it_stores_preview_dates_and_recalculates_project_debt(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $response = $this->post(route('invoice.save'), [
            'khachHang' => 'Khách import',
            'project_name' => 'Công trình import',
            'ngayCuoi' => '16/11/2026',
            'items' => base64_encode(json_encode([
                [
                    'ngay_thang' => '05/11/2026',
                    'ten_vat_lieu' => 'Xi măng',
                    'don_vi_tinh' => 'bao',
                    'so_luong' => 2,
                    'don_gia' => 100000,
                ],
            ])),
            'advance_payments' => base64_encode(json_encode([
                ['ngay_thang' => '2026-11-16', 'noi_dung' => 'Ứng', 'so_tien' => 50000],
            ])),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('invoices', ['invoice_date' => '2026-11-16 00:00:00']);
        $this->assertDatabaseHas('invoice_items', ['product_name' => 'Xi măng', 'date' => '2026-11-05']);
        $this->assertDatabaseHas('payments', ['payment_date' => '2026-11-16']);
        $this->assertDatabaseHas('projects', [
            'name' => 'Công trình import',
            'total_invoice' => 200000,
            'total_paid' => 50000,
            'total_debt' => 150000,
        ]);

        $invoice = Invoice::firstOrFail();
        $this->get(route('invoices.show', $invoice))->assertOk()->assertSee('Ngày giao hàng')->assertSee('05/11/2026');
        $editItems = $this->get(route('invoices.edit', $invoice))->assertOk()->viewData('editItems');
        $this->assertSame('2026-11-05', $editItems[0]['date']);
        $this->put(route('invoices.update', $invoice), [
            'invoice_date' => '2026-11-16',
            'items' => $editItems->all(),
        ])->assertRedirect(route('projects.invoices.index', $invoice->project));
        $this->assertDatabaseHas('invoice_items', ['invoice_id' => $invoice->id, 'date' => '2026-11-05']);
    }
}
