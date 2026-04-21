<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use NumberFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;

class ImportInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('import-invoice-mobile', compact('projectId'));
        }
        return view('import-invoice', compact('projectId'));
    }

    public function import(Request $request)
    {
        $projectId = $request->input('project_id');

        // Check if we are resuming from a stored file (step 2)
        if ($request->has('stored_file_path')) {
            $filePath = storage_path('app/' . $request->input('stored_file_path'));
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'Temporary file expired or not found. Please upload again.');
            }
            $spreadsheet = IOFactory::load($filePath);
            
            // cleanup temp file later or keep it? 
            // Better keep it until successfully imported or simple job. 
            // For now, let's load it.
        } else {
            // Step 1: Upload fresh file
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls',
            ]);
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
        }

        $sheetCount = $spreadsheet->getSheetCount();
        $sheetNames = $spreadsheet->getSheetNames();
        $sheetIndex = $request->input('sheet_index');

        // If multiple sheets and no specific sheet selected, ask user
        if ($sheetCount > 1 && $sheetIndex === null) {
            // Store file temporarily to allow user to pick sheet
            if ($request->has('file')) {
                $path = $request->file('file')->store('temp_imports');
            } else {
                // If somehow came here without file (shouldn't happen in Step 1 w/o stored), reuse stored
                $path = $request->input('stored_file_path');
            }

            $agent = new \Jenssegers\Agent\Agent();
            if ($agent->isMobile()) {
                return view('import-sheet-selection-mobile', [
                    'sheetNames' => $sheetNames,
                    'storedFilePath' => $path,
                    'projectId' => $projectId,
                ]);
            }

            return view('import-sheet-selection', [
                'sheetNames' => $sheetNames,
                'storedFilePath' => $path,
                'projectId' => $projectId,
            ]);
        }

        // Determine which sheet to load
        $targetIndex = $sheetIndex ?? 0;
        $sheet = $spreadsheet->getSheet($targetIndex);

        $data = [];
        $advancePayments = [];
        $stt = 1;
        $tongTien = 0;
        $tongTienUng = 0;
        $ngayCuoi = null;
        
        // Clean up temp file if we used one
        if ($request->has('stored_file_path')) {
            \Illuminate\Support\Facades\Storage::delete($request->input('stored_file_path'));
        }

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
            if (empty($tenVL) || !is_numeric($soLuong)) continue;

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
                    $ts = strtotime(str_replace('.', '-', $dateStr));
                    $ngay = $ts ? date('d/m/Y', $ts) : $dateStr;
                }
            }
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

        // Tổng tiền đọc thành chữ
        $tongTienChu = $this->numberToVietnamese($tongTien);

        $agent = new \Jenssegers\Agent\Agent();
        if ($agent->isMobile()) {
            return view('import-result-mobile', [
                'khachHang'   => str_replace('_', '', str_replace('Khách hàng: ','',$khachHang)),
                'diaChi'      => str_replace('_', '',  str_replace('Địa chỉ: ','',$diaChi)),
                'dienThoai'   => str_replace('_', '', str_replace('Điện thoại:','',$dienThoai)),
                'items'       => $data,
                'advancePayments' => $advancePayments,
                'tongTien'    => $tongTien,
                'tongTienUng' => $tongTienUng,
                'tongTienChu' => $tongTienChu,
                'ngayCuoi'    => $ngayCuoi,
                'projectId'   => $projectId,
            ]);
        }

        return view('import-result', [
            'khachHang'   => str_replace('_', '', str_replace('Khách hàng: ','',$khachHang)),
            'diaChi'      => str_replace('_', '',  str_replace('Địa chỉ: ','',$diaChi)),
            'dienThoai'   => str_replace('_', '', str_replace('Điện thoại:','',$dienThoai)),
            'items'       => $data,
            'advancePayments' => $advancePayments,
            'tongTien'    => $tongTien,
            'tongTienUng' => $tongTienUng,
            'tongTienChu' => $tongTienChu,
            'ngayCuoi'    => $ngayCuoi,
            'projectId'   => $projectId, // Pass project_id result view
        ]);
    }

    private function numberToVietnamese($number)
    {
        $formatter = new NumberFormatter('vi', NumberFormatter::SPELLOUT);
        $text = ucfirst($formatter->format($number));
        return $text . ' đồng';
    }


    public function save(Request $request)
    {
        // Giải mã items đã được chuyển từ preview
        $items = json_decode(base64_decode($request->input('items')), true) ?: [];
        $advancePayments = json_decode(base64_decode($request->input('advance_payments')), true) ?: [];

        // Lấy thông tin từ form (nếu có)
        $customerName = trim($request->input('khachHang', 'Khách hàng chưa rõ'));
        $customerAddress = trim($request->input('diaChi', ''));
        $customerPhone = trim($request->input('dienThoai', ''));
        $projectName = trim($request->input('project_name', 'Công trình - ' . $customerName));
        $invoiceDate = $request->input('ngayCuoi', now()->format('Y-m-d'));
        $totalAmountFromForm = (float) $request->input('tongTien', 0);
        $totalAmountText = $request->input('tongTienChu', '');

        // Payment info (tùy chọn)
        $paymentAmount = (float) $request->input('payment_amount', 0);
        $paymentType = $request->input('payment_type', 'tung_dot');
        $paymentFor = $request->input('payment_for', 'project'); // 'project' | 'all'
        $paymentDate = $request->input('payment_date', now()->format('Y-m-d'));

        DB::beginTransaction();

        try {
            // 1) Tìm hoặc tạo khách hàng
            $customer = Customer::firstOrCreate(
                ['name' => $customerName],
                ['address' => $customerAddress, 'phone' => $customerPhone]
            );

            // 2) Tìm hoặc tạo công trình thuộc khách hàng
            // Nếu bạn gửi project_id trong form, ưu tiên dùng project_id
            $project = null;
            if ($request->filled('project_id')) {
                $project = Project::find($request->input('project_id'));
            }
            if (!$project) {
                // IMPORTANT: If creating new project, ensure we use the FOUND customer ID
                $project = Project::firstOrCreate(
                    ['customer_id' => $customer->id, 'name' => $projectName],
                    ['address' => $customerAddress, 'note' => 'Tạo tự động khi import']
                );
            }

            // 3) Tạo hóa đơn
            // Nếu tổng từ form = 0, tính lại từ items
            $calculatedTotal = 0;
            foreach ($items as $it) {
                $calculatedTotal += isset($it['thanh_tien']) ? (float)$it['thanh_tien'] : ((float)$it['so_luong'] * (float)$it['don_gia']);
            }
            $invoiceTotal = $totalAmountFromForm > 0 ? $totalAmountFromForm : $calculatedTotal;

            $invoice = Invoice::create([
                'project_id' => $project->id,
                'code' => $request->input('invoice_code', null),
                'invoice_date' => $invoiceDate,
                'total_amount' => $invoiceTotal,
                'total_text' => $totalAmountText,
                'note' => $request->input('invoice_note', null),
            ]);

            // 4) Tạo các invoice_items
            foreach ($items as $it) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'date' => $it['ngay_thang'] ?? null,
                    'product_name' => $it['ten_vat_lieu'] ?? $it['material_name'] ?? null,
                    'unit' => $it['don_vi_tinh'] ?? $it['unit'] ?? null,
                    'quantity' => isset($it['so_luong']) ? (float)$it['so_luong'] : 0,
                    'price' => isset($it['don_gia']) ? (float)$it['don_gia'] : 0,
                    'amount' => isset($it['thanh_tien']) ? (float)$it['thanh_tien'] : ((float)$it['so_luong'] * (float)$it['don_gia']),
                ]);
            }
            
            // Xử lý Advance Payments (Ứng tiền từ file Excel)
            foreach ($advancePayments as $ap) {
                $pDate = $ap['ngay_thang'] ? \Carbon\Carbon::createFromFormat('d/m/Y', $ap['ngay_thang'])->format('Y-m-d') : $invoiceDate;
                Payment::create([
                    'project_id' => $project->id,
                    'payment_date' => $pDate,
                    'amount' => (float)$ap['so_tien'],
                    'payment_type' => 'ung_truoc',
                    'for_project' => 'project',
                    'note' => $ap['noi_dung'] . ' (Import từ file)',
                ]);
            }

            // 5) Cập nhật tổng cho project (tăng total_invoice, tính total_debt)
            // Tính lại tổng hóa đơn của project từ DB (an toàn nếu có nhiều hóa đơn)
            $totalInvoiceSum = (float) Invoice::where('project_id', $project->id)->sum('total_amount');
            
            // Tổng thanh toán hiện có trên project (bao gồm cả advance payments vừa tạo)
            $totalPaidSum = (float) Payment::where('project_id', $project->id)->sum('amount');

            // Nếu sẽ có payment trong cùng request, thêm tạm trước khi lưu project
            $futurePaid = $paymentAmount;
            $project->total_invoice = $totalInvoiceSum;
            $project->total_paid = $totalPaidSum + $futurePaid;
            $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
            $project->save();

            // 6) Xử lý payment (nếu có)
            if ($paymentAmount > 0) {
                if ($paymentFor === 'project') {
                    // Tạo 1 payment gắn vào project
                    $p = Payment::create([
                        'project_id' => $project->id,
                        'payment_date' => $paymentDate,
                        'amount' => $paymentAmount,
                        'payment_type' => $paymentType,
                        'for_project' => 'project',
                        'note' => $request->input('payment_note', 'Thanh toán khi import'),
                    ]);

                    // cập nhật project (đã tạm cộng phía trên, nhưng chắc chắn cập nhật lại từ DB)
                    $project->total_paid = (float) Payment::where('project_id', $project->id)->sum('amount');
                    $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
                    $project->save();
                } else {
                    // payment_for == 'all' => phân bổ thanh toán cho tất cả công trình của cùng khách hàng
                    // Strategy: lấy danh sách công trình của customer theo thứ tự "cũ nhất (invoice_date nhỏ nhất) first"
                    $remaining = $paymentAmount;
                    $projects = Project::where('customer_id', $customer->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($projects as $pr) {
                        if ($remaining <= 0) break;

                        // Tính nợ hiện tại của công trình (từ DB)
                        $prInvoiceSum = (float) Invoice::where('project_id', $pr->id)->sum('total_amount');
                        $prPaidSum = (float) Payment::where('project_id', $pr->id)->sum('amount');
                        $prDebt = max(0, $prInvoiceSum - $prPaidSum);

                        if ($prDebt <= 0) continue; // không nợ

                        $apply = min($prDebt, $remaining);

                        Payment::create([
                            'project_id' => $pr->id,
                            'payment_date' => $paymentDate,
                            'amount' => $apply,
                            'payment_type' => $paymentType,
                            'for_project' => 'all',
                            'note' => 'Phân bổ thanh toán tổng cho nhiều công trình (import)',
                        ]);

                        // cập nhật project tức thì
                        $pr->total_invoice = $prInvoiceSum;
                        $pr->total_paid = (float) Payment::where('project_id', $pr->id)->sum('amount');
                        $pr->total_debt = max(0, $pr->total_invoice - $pr->total_paid);
                        $pr->save();

                        $remaining -= $apply;
                    }

                    // Nếu vẫn còn remaining > 0 (không còn công trình nợ), tạo payment tổng gắn project_id = null
                    if ($remaining > 0) {
                        Payment::create([
                            'project_id' => $project->id, // lưu vào project hiện tại làm "kho chứa" tạm — hoặc set null nếu migration cho phép
                            'payment_date' => $paymentDate,
                            'amount' => $remaining,
                            'payment_type' => $paymentType,
                            'for_project' => 'all',
                            'note' => 'Số dư chưa phân bổ (import)',
                        ]);

                        // cập nhật project hiện tại lại
                        $project->total_paid = (float) Payment::where('project_id', $project->id)->sum('amount');
                        $project->total_debt = max(0, $project->total_invoice - $project->total_paid);
                        $project->save();
                    }
                }
            }

            DB::commit();

            // Redirect back to project view if project_id exists
            if ($project) {
                return redirect()->route('customers.projects.index', $project->customer_id)
                    ->with('success', 'Import và lưu hóa đơn thành công.');
            }

            return redirect()->route('invoice.index')->with('success', 'Import và lưu hóa đơn thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // log lỗi để debug
            \Log::error('Invoice import save error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Lưu dữ liệu thất bại: ' . $e->getMessage());
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
            'total_invoice' => $data['tongTien'],
            'total_paid' => 0,
            'total_debt' => $data['tongTien'],
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

        return redirect()->route('invoice.index')->with('success', 'Import và lưu dữ liệu thành công!');
    }
}
