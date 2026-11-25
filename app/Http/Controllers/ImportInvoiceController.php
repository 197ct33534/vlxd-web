<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use NumberFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Services\InvoiceImportMatcher;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Services\PaymentAllocationService;
use App\Services\ProjectDebtService;

class ImportInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $importProject = Project::find($request->integer('project_id'));
        $projectId = $importProject?->id;
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('import-invoice-mobile', compact('projectId', 'importProject'));
        }
        return view('import-invoice', compact('projectId', 'importProject'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);
        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheets = [];
            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $sheets[] = ['name' => $sheet->getTitle()] + $this->readSheet($sheet);
            }
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return back()->with('error', 'Không đọc được tệp Excel. Hãy kiểm tra định dạng và tải lại.');
        }

        $token = (string) Str::uuid();
        Cache::put('invoice-import:'.$token, [
            'user' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'sheets' => $sheets,
        ], now()->addHours(2));

        return redirect()->route('invoice.workbook', array_filter([
            'token' => $token,
            'project_id' => $request->input('project_id'),
        ]));
    }

    public function workbook(Request $request, string $token, InvoiceImportMatcher $matcher)
    {
        $workbook = $this->loadWorkbook($request, $token);
        if (!$workbook) {
            return redirect()->route('invoice.index')->with('error', 'Bản xem trước đã hết hạn. Hãy tải lại tệp Excel.');
        }

        $projects = Project::with('customer:id,name')->orderBy('name')->get();
        $project = $projects->firstWhere('id', $request->integer('project_id'));
        $invoices = $project?->invoices()->with('items')->latest('id')->get();
        $seen = [];
        $sheets = [];
        foreach ($workbook['sheets'] as $index => $sheet) {
            if (!$sheet['items'] && !$sheet['advancePayments']) {
                $match = ['status' => 'empty'];
            } else {
                try {
                    $hash = $matcher->fingerprint($sheet['items'], $sheet['advancePayments'], $sheet['tongTien']);
                    $match = $matcher->match(
                        $project, $sheet['items'], $sheet['tongTien'], $hash,
                        $matcher->sourceKey($workbook['filename'], $sheet['name']), $invoices
                    );
                    if ($match['status'] === 'ready' && isset($seen[$hash])) {
                        $match = ['status' => 'same_sheet', 'sheet' => $seen[$hash]];
                    }
                    $seen[$hash] ??= $sheet['name'];
                } catch (\InvalidArgumentException $e) {
                    $match = ['status' => 'invalid', 'error' => $e->getMessage()];
                }
            }
            $sheets[$index] = $sheet + ['match' => $match];
        }

        return view('import-workbook', compact('token', 'workbook', 'projects', 'project', 'sheets'));
    }

    public function saveWorkbook(Request $request, string $token)
    {
        $workbook = $this->loadWorkbook($request, $token);
        if (!$workbook) {
            return redirect()->route('invoice.index')->with('error', 'Bản xem trước đã hết hạn. Hãy tải lại tệp Excel.');
        }

        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id', 'required_without:project_name', 'prohibits:project_name'],
            'project_name' => ['nullable', 'string', 'max:255', 'required_without:project_id'],
            'sheets' => ['required', 'array', 'min:1'],
            'sheets.*' => ['required', 'integer', 'distinct', 'min:0', 'max:'.(count($workbook['sheets']) - 1)],
            'reviewed' => ['sometimes', 'array'],
            'reviewed.*' => ['required', 'integer'],
            'invoice_dates' => ['sometimes', 'array'],
            'invoice_dates.*' => ['nullable', 'date_format:Y-m-d'],
            'confirmed' => ['accepted'],
        ], [
            'project_id.required_without' => 'Hãy chọn công trình có sẵn hoặc nhập tên công trình mới.',
            'project_name.required_without' => 'Hãy nhập tên công trình mới hoặc chọn công trình có sẵn.',
            'sheets.required' => 'Hãy chọn ít nhất một sheet để lưu.',
            'confirmed.accepted' => 'Hãy kiểm tra công trình và ngày trên sheet trước khi xác nhận.',
        ]);

        $projectId = $data['project_id'] ?? null;
        if (!$projectId) {
            $customers = [];
            foreach ($data['sheets'] as $index) {
                $customers[] = mb_strtolower($workbook['sheets'][$index]['khachHang']);
            }
            if (count(array_unique($customers)) !== 1 || $customers[0] === '') {
                return back()->withErrors(['project_name' => 'Khi tạo công trình mới, các sheet đã chọn phải có cùng một khách hàng.'])->withInput();
            }
        }

        $results = [];
        foreach ($data['sheets'] as $index) {
            $sheet = $workbook['sheets'][$index];
            try {
                if (!$sheet['items'] && !$sheet['advancePayments']) {
                    throw new \InvalidArgumentException('Sheet không có dòng vật liệu hoặc ứng tiền.');
                }

                $invoiceDate = $data['invoice_dates'][$index] ?? $sheet['invoiceDate'] ?? null;
                if (!$invoiceDate) {
                    throw new \InvalidArgumentException('Hãy nhập ngày xuất hóa đơn cho sheet này.');
                }

                $result = $this->persistInvoice(new Request([
                    'project_id' => $projectId,
                    'project_name' => $data['project_name'] ?? null,
                    'khachHang' => $sheet['khachHang'],
                    'diaChi' => $sheet['diaChi'],
                    'dienThoai' => $sheet['dienThoai'],
                    'invoice_date' => $invoiceDate,
                    'tongTienChu' => $sheet['tongTienChu'],
                ]), ['filename' => $workbook['filename']] + $sheet, in_array($index, $data['reviewed'] ?? []));
                $projectId = $result['project']->id;
                $results[$index] = ['status' => $result['duplicate'] ? 'duplicate' : 'saved', 'invoice_id' => $result['invoice']->id];
            } catch (\InvalidArgumentException $e) {
                $results[$index] = ['status' => 'error', 'error' => $e->getMessage()];
            } catch (\Throwable $e) {
                report($e);
                $results[$index] = ['status' => 'error', 'error' => 'Không lưu được sheet. Hãy thử lại.'];
            }
        }

        return redirect()->route('invoice.workbook', [
            'token' => $token,
            'project_id' => $projectId,
        ])->with('sheet_results', $results)
            ->withInput($request->only('invoice_dates') + ($projectId ? [] : $request->only('project_name', 'sheets', 'reviewed')));
    }

    private function loadWorkbook(Request $request, string $token): ?array
    {
        $workbook = Cache::get('invoice-import:'.$token);
        abort_if($workbook && $workbook['user'] !== $request->user()->id, 404);

        return $workbook;
    }

    private function readSheet(Worksheet $sheet): array
    {
        $data = [];
        $advancePayments = [];
        $stt = 1;
        $tongTien = 0;
        $tongTienUng = 0;
        $ngayCuoi = null;
        $invoiceDates = [];
        
        // Duyệt toàn bộ các dòng
        foreach ($sheet->getRowIterator() as $row) {
            $r = $row->getRowIndex();

            // Lấy giá trị các cột (A-G)
            $ngayRaw   = $sheet->getCell("B{$r}")->getCalculatedValue();
            $tenVL     = $sheet->getCell("C{$r}")->getValue();
            $donVi     = $sheet->getCell("D{$r}")->getValue();
            $soLuong   = $sheet->getCell("E{$r}")->getCalculatedValue();
            $donGia    = $sheet->getCell("F{$r}")->getCalculatedValue();

            // Bỏ qua nếu không có dữ liệu hợp lệ
            if (empty($tenVL) || !is_numeric($soLuong)) {
                foreach (range('A', 'G') as $column) {
                    $value = (string) $sheet->getCell($column.$r)->getCalculatedValue();
                    if (preg_match('/^\s*Ngày\s*(?:(?:xuất|lập)\s+hóa\s+đơn\s*:?\s*)?(\d{1,2})\s*(?:tháng\s*|[\/.\-]\s*)(\d{1,2})\s*(?:năm\s*|[\/.\-]\s*)(\d{4})\s*$/iu', $value, $parts)) {
                        [, $day, $month, $year] = $parts;
                        if (checkdate((int) $month, (int) $day, (int) $year)) {
                            $invoiceDates[sprintf('%04d-%02d-%02d', $year, $month, $day)] = true;
                        }
                    }
                }
                continue;
            }

            // Xử lý ngày (chuẩn theo code cũ hoạt động đúng)
            $ngay = null;
            if ($ngayRaw !== null && $ngayRaw !== '') {
                if (is_numeric($ngayRaw)) {
                    try {
                        $dt = ExcelDate::excelToDateTimeObject((float)$ngayRaw);
                        $ngay = $dt->format('d/m/Y');
                    } catch (\Exception $e) {
                        $ngay = trim((string)$ngayRaw);
                    }
                } else {
                    $dateStr = trim((string)$ngayRaw);
                    try {
                        $normalized = $this->normalizeImportDate($dateStr);
                        $ngay = $normalized ? Carbon::parse($normalized)->format('d/m/Y') : null;
                    } catch (\InvalidArgumentException $e) {
                        $ngay = $dateStr;
                    }
                }
            }
            // Thành tiền
            $thanhTien = (float)$soLuong * (float)$donGia;

            // Check for advance payment (ứng tiền)
            $lowerName = mb_strtolower($tenVL, 'UTF-8');
            $isAdvance = false;
            $advanceKeywords = ['ứng', 'trả trước', 'ck', 'chuyển khoản', 'đặt cọc'];
            foreach ($advanceKeywords as $kw) {
                if (str_contains($lowerName, $kw) && !str_contains($lowerName, 'ứng dụng')) { // exclude false positives if any
                    $isAdvance = true;
                    break;
                }
            }

            if ($isAdvance) {
                $tongTienUng += $thanhTien;
                $advancePayments[] = [
                    'ngay_thang' => $ngay,
                    'noi_dung'   => trim($tenVL),
                    'so_tien'    => $thanhTien,
                ];
            } else {
                if ($ngay) {
                    // Parse date for comparison (d/m/Y -> Y-m-d)
                    try {
                        $currentDate = Carbon::createFromFormat('d/m/Y', $ngay);
                        if (!$ngayCuoi) {
                            $ngayCuoi = $ngay;
                        } else {
                            $lastDate = Carbon::createFromFormat('d/m/Y', $ngayCuoi);
                            if ($currentDate->gt($lastDate)) {
                                $ngayCuoi = $ngay;
                            }
                        }
                    } catch (\Exception $e) {
                        // Fallback string compare or ignore
                    }
                }
                $tongTien += $thanhTien;
                $data[] = [
                    'stt'          => $stt++,
                    'ngay_thang'   => $ngay,
                    'ten_vat_lieu' => trim($tenVL),
                    'don_vi_tinh'  => trim($donVi),
                    'so_luong'     => (float)$soLuong,
                    'don_gia'      => (float)$donGia,
                    'thanh_tien'   => $thanhTien,
                ];
            }
        }

        // Thông tin khách hàng (lấy ở vùng trên cùng)
        $khachHangCell = $sheet->getCell('A5')->getValue();
        $khachHang = $khachHangCell instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText
            ? trim($khachHangCell->getPlainText())
            : trim((string)$khachHangCell);

        $diaChi = trim((string)($sheet->getCell('A6')->getValue() ?? ''));
        $dienThoai = trim((string)($sheet->getCell('F6')->getValue() ?? ''));

        return [
            'khachHang' => trim(str_replace(['Khách hàng:', '_'], '', $khachHang)),
            'diaChi' => trim(str_replace(['Địa chỉ:', '_'], '', $diaChi)),
            'dienThoai' => trim(str_replace(['Điện thoại:', '_'], '', $dienThoai)),
            'items' => $data,
            'advancePayments' => $advancePayments,
            'tongTien' => $tongTien,
            'tongTienUng' => $tongTienUng,
            'tongTienChu' => $this->numberToVietnamese($tongTien),
            'ngayCuoi' => $ngayCuoi,
            'invoiceDate' => count($invoiceDates) === 1 ? array_key_first($invoiceDates) : null,
        ];
    }

    private function numberToVietnamese($number)
    {
        $formatter = new NumberFormatter('vi', NumberFormatter::SPELLOUT);
        $text = ucfirst($formatter->format($number));
        return $text . ' đồng';
    }

    private function normalizeImportDate(?string $value): ?string
    {
        return app(InvoiceImportMatcher::class)->normalizeDate($value);
    }

    public function save(Request $request)
    {
        $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id', 'required_without:project_name', 'prohibits:project_name'],
            'project_name' => ['nullable', 'string', 'max:255', 'required_without:project_id'],
            'khachHang' => ['nullable', 'string', 'max:255', 'required_without:project_id'],
        ], [
            'project_id.required_without' => 'Hãy chọn công trình có sẵn hoặc nhập tên công trình mới.',
            'project_id.exists' => 'Công trình đã chọn không còn tồn tại.',
            'project_name.required_without' => 'Hãy nhập tên công trình mới hoặc chọn công trình có sẵn.',
            'project_id.prohibits' => 'Chỉ chọn một cách lưu hóa đơn vào công trình.',
            'khachHang.required_without' => 'Cần có tên khách hàng trong file Excel để tạo công trình mới.',
        ]);

        try {
            $result = $this->persistInvoice($request);
            return redirect()->route('customers.projects.index', $result['project']->customer_id)
                ->with('success', $result['duplicate'] ? 'Sheet đã có hóa đơn #'.$result['invoice']->id.'.' : __('msg.import_success'));
        } catch (\Throwable $e) {
            return back()->with('error', __('msg.import_save_failed', ['error' => $e->getMessage()]));
        }
    }

    private function persistInvoice(Request $request, ?array $source = null, bool $reviewed = false): array
    {
        // Giải mã items đã được chuyển từ preview
        $items = $source['items'] ?? (json_decode(base64_decode($request->input('items', '')), true) ?: []);
        $advancePayments = $source['advancePayments'] ?? (json_decode(base64_decode($request->input('advance_payments', '')), true) ?: []);

        // Lấy thông tin từ form (nếu có)
        $customerName = trim($request->input('khachHang', 'Khách hàng chưa rõ'));
        $customerAddress = trim($request->input('diaChi', ''));
        $customerPhone = trim($request->input('dienThoai', ''));
        $projectName = $request->input('project_name');
        $invoiceDateInput = $request->input('invoice_date', $request->input('ngayCuoi'));
        $totalAmountFromForm = (float) $request->input('tongTien', 0);
        $totalAmountText = $request->input('tongTienChu', '');

        // Payment info (tùy chọn)
        $paymentAmount = (float) $request->input('payment_amount', 0);
        $paymentType = $request->input('payment_type', 'tung_dot');
        $paymentFor = $request->input('payment_for', 'project'); // 'project' | 'all'

        DB::beginTransaction();

        try {
            $invoiceDate = $this->normalizeImportDate($invoiceDateInput) ?? today()->toDateString();

            if ($request->filled('project_id')) {
                $project = Project::lockForUpdate()->findOrFail($request->input('project_id'));
                $customer = $project->customer;
            } else {
                $customer = Customer::firstOrCreate(
                    ['name' => $customerName],
                    ['address' => $customerAddress, 'phone' => $customerPhone]
                );
                $customer = Customer::lockForUpdate()->findOrFail($customer->id);
                if ($customer->projects()->where('name', $projectName)->exists()) {
                    throw new \InvalidArgumentException('Công trình này đã có. Hãy chọn công trình đó trong danh sách.');
                }
                $project = $customer->projects()->create([
                    'name' => $projectName,
                    'address' => $customerAddress,
                ]);
            }

            // 3) Tạo hóa đơn
            // Nếu tổng từ form = 0, tính lại từ items
            $calculatedTotal = 0;
            foreach ($items as $it) {
                $calculatedTotal += isset($it['thanh_tien']) ? (float)$it['thanh_tien'] : ((float)$it['so_luong'] * (float)$it['don_gia']);
            }
            $invoiceTotal = $totalAmountFromForm > 0 ? $totalAmountFromForm : $calculatedTotal;

            $matcher = app(InvoiceImportMatcher::class);
            $hash = $matcher->fingerprint($items, $advancePayments, $invoiceTotal);
            $sourceKey = $source ? $matcher->sourceKey($source['filename'], $source['name']) : null;
            $match = $matcher->match($project, $items, $invoiceTotal, $hash, $sourceKey);
            if ($match['status'] === 'duplicate') {
                DB::commit();
                return ['project' => $project, 'invoice' => $match['invoice'], 'duplicate' => true];
            }
            if (!$reviewed && in_array($match['status'], ['legacy', 'changed', 'overlap', 'other_project'], true)) {
                throw new \InvalidArgumentException('Sheet có dữ liệu cần đối chiếu. Hãy xem lại và xác nhận trước khi nhập.');
            }

            $invoice = new Invoice([
                'project_id' => $project->id,
                'code' => $request->input('invoice_code', null),
                'invoice_date' => $invoiceDate,
                'total_amount' => $invoiceTotal,
                'total_text' => $totalAmountText,
                'note' => $request->input('invoice_note', null),
            ]);

            $invoice->forceFill([
                'import_hash' => $hash,
                'import_source_key' => $sourceKey,
                'import_filename' => $source ? mb_substr($source['filename'], 0, 255) : null,
                'import_sheet' => $source['name'] ?? null,
            ])->save();

            // 4) Tạo các invoice_items
            foreach ($items as $it) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'date' => $this->normalizeImportDate($it['ngay_thang'] ?? null),
                    'product_name' => $it['ten_vat_lieu'] ?? $it['material_name'] ?? null,
                    'unit' => $it['don_vi_tinh'] ?? $it['unit'] ?? null,
                    'quantity' => isset($it['so_luong']) ? (float)$it['so_luong'] : 0,
                    'price' => isset($it['don_gia']) ? (float)$it['don_gia'] : 0,
                    'amount' => isset($it['thanh_tien']) ? (float)$it['thanh_tien'] : ((float)$it['so_luong'] * (float)$it['don_gia']),
                ]);
            }
            
            $touchedProjectIds = [$project->id];
            $paymentType = Payment::normalizeType($paymentType);

            // Ứng tiền từ file Excel. Enum chưa có ung_truoc nên lưu tung_dot.
            foreach ($advancePayments as $ap) {
                $pDate = $this->normalizeImportDate($ap['ngay_thang'] ?? null) ?? $invoiceDate;
                $advance = Payment::create([
                    'project_id' => $project->id,
                    'payment_date' => $pDate,
                    'amount' => (float)$ap['so_tien'],
                    'payment_type' => Payment::normalizeType('ung_truoc'),
                    'for_project' => 'project',
                    'note' => $ap['noi_dung'] . ' (Import từ file)',
                ]);
                app(PaymentAllocationService::class)->allocate($advance);
            }

            if ($paymentAmount > 0) {
                $paymentDate = $this->normalizeImportDate($request->input('payment_date')) ?? today()->toDateString();

                if ($paymentFor === 'project') {
                    $imported = Payment::create([
                        'project_id' => $project->id,
                        'payment_date' => $paymentDate,
                        'amount' => $paymentAmount,
                        'payment_type' => $paymentType,
                        'for_project' => 'project',
                        'note' => $request->input('payment_note', 'Thanh toán khi import'),
                    ]);
                    app(PaymentAllocationService::class)->allocate($imported);
                } else {
                    $remaining = $paymentAmount;
                    $projects = Project::where('customer_id', $customer->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($projects as $pr) {
                        if ($remaining <= 0) {
                            break;
                        }

                        $prInvoiceSum = (float) Invoice::where('project_id', $pr->id)->sum('total_amount');
                        $prPaidSum = (float) Payment::where('project_id', $pr->id)->sum('amount');
                        $prDebt = max(0, $prInvoiceSum - $prPaidSum);

                        if ($prDebt <= 0) {
                            continue;
                        }

                        $apply = min($prDebt, $remaining);

                        $split = Payment::create([
                            'project_id' => $pr->id,
                            'payment_date' => $paymentDate,
                            'amount' => $apply,
                            'payment_type' => $paymentType,
                            'for_project' => 'all',
                            'note' => 'Phân bổ thanh toán tổng cho nhiều công trình (import)',
                        ]);
                        app(PaymentAllocationService::class)->allocate($split);

                        $touchedProjectIds[] = $pr->id;
                        $remaining -= $apply;
                    }

                    if ($remaining > 0) {
                        $leftover = Payment::create([
                            'project_id' => $project->id,
                            'payment_date' => $paymentDate,
                            'amount' => $remaining,
                            'payment_type' => $paymentType,
                            'for_project' => 'all',
                            'note' => 'Số dư chưa phân bổ (import)',
                        ]);
                        app(PaymentAllocationService::class)->allocate($leftover);
                    }
                }
            }

            $debts = app(ProjectDebtService::class);
            foreach (array_unique($touchedProjectIds) as $projectId) {
                $debts->recalculate($projectId);
            }

            DB::commit();

            return ['project' => $project, 'invoice' => $invoice, 'duplicate' => false];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function saveSimple(Request $request)
    {
        $data = $request->all();

        // 1️⃣ Tạo hoặc lấy Customer
        $customer = Customer::firstOrCreate(
            ['name' => $data['khachHang']],
            [
                'phone' => $data['dienThoai'] ?? null,
                'address' => $data['diaChi'] ?? null
            ]
        );

        // 2️⃣ Tạo Project cho khách hàng (có thể mỗi file tương ứng 1 công trình)
        $project = Project::create([
            'customer_id' => $customer->id,
            'name' => 'Công trình ' . $data['khachHang'],
            'address' => $data['diaChi'],
            'end_date' => $data['ngayCuoi'],
        ]);

        // 3️⃣ Tạo Invoice
        $invoice = Invoice::create([
            'project_id' => $project->id,
            'invoice_date' => $data['ngayCuoi'],
            'total_amount' => $data['tongTien'],
            'total_text' => $data['tongTienChu'],
        ]);

        // 4️⃣ Tạo InvoiceItems
        $items = json_decode(base64_decode($request->input('items')), true) ?: [];

        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'date' => $item['ngay_thang'],
                'product_name' => $item['ten_vat_lieu'],
                'unit' => $item['don_vi_tinh'],
                'quantity' => $item['so_luong'],
                'price' => $item['don_gia'],
                'amount' => $item['thanh_tien'],
            ]);
        }

        app(ProjectDebtService::class)->recalculate($project->id);

        return redirect()->route('invoice.index')->with('success', __('msg.import_success_alt'));
    }
}
