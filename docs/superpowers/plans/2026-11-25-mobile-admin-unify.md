# Đồng bộ giao diện mobile phần quản trị — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 9 view mobile độc lập của phần quản trị chạy trong `layouts.dashboard`, cùng màu/font/menu với màn Tổng quan, tiếng Việt hoàn toàn, giữ nguyên chức năng.

**Architecture:** Mỗi view giữ file riêng (controller vẫn chọn view theo User-Agent) nhưng đổi thành `@extends('layouts.dashboard')` với `title`, `page_heading`, `content`; bỏ khung tự chế (head, header, bottom nav, footer, overlay loading, sidebar riêng). Loading dùng `isLoading` của layout. Kiểm chứng bằng Pest feature test chung một bộ assertion.

**Tech Stack:** Laravel 10 Blade, Tailwind 3 (theme `tailwind.admin.config.js` qua `resources/css/admin.css`), Alpine 3 (bundle `resources/js/alpine.js`), Pest 2. Chạy PHP bằng `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe` (PHP mặc định 8.2 không chạy được vendor).

**Spec:** `docs/superpowers/specs/2026-11-25-mobile-admin-unify-design.md`

## Global Constraints

- Màu chủ đạo `#007BFF`, font Inter, bo góc admin — chỉ qua `admin.css` mà layout dashboard đã nạp; view không tự `@vite` CSS.
- Mọi chữ hiển thị tiếng Việt; không còn chuỗi `CMS Inc.`.
- Không sửa controller, route, model, dữ liệu, view desktop. Chỉ ngoại lệ: thêm `print:hidden` vào `layouts/dashboard.blade.php` (Task 4).
- Không còn trong 9 view: `<!DOCTYPE`, `<x-mobile-sidebar`, `material-icons-outlined`, bottom nav, `isLoading`/`loading` trong `x-data` của trang.
- Form submit bật loading bằng `@submit="isLoading = true"` (ghi vào biến của layout).
- Mỗi task: chạy toàn bộ `vendor/bin/pest` và `npm run build` trước khi commit; commit theo kiểu branch + fast-forward `main` như các commit trước.

## Review Focus

- Modal thêm/sửa/xóa khách hàng sau khi `x-data` rời `<body>`: phải mở được và nằm trên topbar (z-index ≥ 50) — kiểm bằng trình duyệt ở Task 2.
- Upload Excel / chọn sheet / lưu import: bấm gửi phải hiện overlay loading chung, không bị kẹt overlay khi validation lỗi quay lại trang — test Task 6 kiểm `@submit="isLoading = true"` có mặt; trình duyệt kiểm hiện overlay.
- Thanh lưu hóa đơn `.sticky-actions` và FAB không bị che, không che nội dung cuối trang ở 375px — trình duyệt Task 2, 5.
- In hóa đơn chỉ ra phần hóa đơn — test Task 4 kiểm `print:hidden` trên sidebar/topbar/footer.
- Desktop không đổi — mỗi test file có một ca User-Agent desktop vẫn trả view desktop (Task 1 helper).

---

### Task 1: Bộ helper test dùng chung

**Files:**
- Modify: `tests/Pest.php`
- Modify: `tests/Feature/MobileSidebarTest.php`, `tests/Feature/CustomerMobileListTest.php` (bỏ beforeEach/afterEach UA tự viết, dùng helper)

**Interfaces:**
- Produces:
  - `useMobileUserAgent(): void` — gọi trong test hoặc `beforeEach`; lưu giá trị cũ của `$_SERVER['HTTP_USER_AGENT']` vào một biến static rồi đặt UA Android Chrome mobile. Một `afterEach` toàn cục trong `tests/Pest.php` (áp cho `Feature`) khôi phục giá trị cũ nếu đã lưu.
  - `actingAsAdmin(): \App\Models\User` — tạo `User::factory()->create(['role' => 'admin'])` và `actingAs`.
  - Expectation `toBeDashboardMobilePage()` trên chuỗi HTML: chứa `mf-admin-main` và `Alpine.store('layout'`; không chứa `<!DOCTYPE html>` hai lần, `CMS Inc`, `material-icons-outlined`, `relative z-[70]` (wrapper chỉ `<x-mobile-sidebar>` sinh ra), và không chứa (so khớp `>\s*TEXT\s*<`) các chữ: `Home`, `Clients`, `Invoices`, `Reports`, `Settings`, `Select Sheet to Import`, `Continue Import`.

- [ ] **Step 1:** Thêm các helper trên vào `tests/Pest.php`; viết lại 2 test file dùng `useMobileUserAgent()`/`actingAsAdmin()`.
- [ ] **Step 2:** Chạy `vendor/bin/pest` — Expected: 12 passed (không đổi hành vi).
- [ ] **Step 3:** Commit `test: share mobile user-agent and dashboard-shell helpers`.

### Task 2: Khách hàng — `customer/mobile`

**Files:**
- Modify: `resources/views/customer/mobile.blade.php`
- Test: `tests/Feature/CustomerMobileListTest.php`

**Interfaces:** Consumes Task 1 helpers. Giữ `data-customer-card="{id}"`, `openAdd()/openEdit()/openDelete()`.

- [ ] **Step 1: Failing test** — thêm `it('renders the mobile customers page inside the dashboard shell')`: `useMobileUserAgent()`, `actingAsAdmin()`, GET `route('customers.index')` → `assertOk()`, `expect($html)->toBeDashboardMobilePage()->toContain('openAdd()')->toContain('data-customer-card')`. Thêm ca desktop (không gọi helper UA): HTML **không** chứa `data-customer-card` (vẫn là view desktop).
- [ ] **Step 2:** Chạy file test — Expected: FAIL ở `toBeDashboardMobilePage`.
- [ ] **Step 3: Chuyển view** theo spec bước 1–10: `@section('title', __('customer.title'))`, `@section('page_heading', __('customer.title'))`; `x-data` của body → `<div x-data="{ modalOpen…, openAdd(), openEdit(), openDelete() }">` bọc toàn bộ content (bỏ `isLoading` và handler `@click` bắt link); bỏ header, bottom nav, `<x-mobile-sidebar />`, overlay loading; giữ `<x-customer.search-filter />`, danh sách thẻ, phân trang, modal; FAB `fixed bottom-6 right-6` cộng `env(safe-area-inset-bottom)`; form trong modal thêm `@submit="isLoading = true"`. Bỏ `<h2>` tiêu đề trùng nếu topbar đã hiện.
- [ ] **Step 4:** `vendor/bin/pest` + `npm run build` — Expected: all pass, build sạch.
- [ ] **Step 5: Trình duyệt 375px** (đăng nhập admin seed, UA mobile): không cuộn ngang; mở sidebar từ topbar; bấm "Thêm khách" và "Sửa" → modal hiện trên topbar, đóng được (không lưu).
- [ ] **Step 6:** Commit `refactor: render mobile customers inside the dashboard layout`.

### Task 3: Dự án theo khách — `projects/mobile_index`

**Files:** Modify `resources/views/projects/mobile_index.blade.php`; Test: Create `tests/Feature/MobileDashboardPagesTest.php`

**Interfaces:** Consumes Task 1. Produces file test `MobileDashboardPagesTest.php` mà Task 4–6 thêm ca vào. Dữ liệu: `Customer::create(['name','phone'])`, `Project::create(['customer_id','name','total_invoice','total_paid','total_debt'])`.

- [ ] **Step 1: Failing test** `it('renders customer projects inside the dashboard shell')`: GET `route('customers.projects.index', $customer)` → `toBeDashboardMobilePage()`, chứa tên dự án và `route('customers.index')` (link quay lại).
- [ ] **Step 2:** Chạy — Expected: FAIL.
- [ ] **Step 3: Chuyển view** (spec 1–10): `title`/`page_heading` = "Dự án – {{ $customer->name }}"; header có back → dòng đầu content `← Quay lại` trỏ `route('customers.index')` + tên khách; giữ modal, `<script>` đặt cuối content; form modal `@submit="isLoading = true"`.
- [ ] **Step 4:** pest + build — pass.
- [ ] **Step 5:** Trình duyệt 375px: không cuộn ngang, nút back, mở modal thêm dự án rồi đóng.
- [ ] **Step 6:** Commit `refactor: render mobile customer projects inside the dashboard layout`.

### Task 4: Hóa đơn — danh sách và chi tiết (+ in)

**Files:** Modify `resources/views/invoices/mobile_index.blade.php`, `resources/views/invoices/mobile_show.blade.php`, `resources/views/layouts/dashboard.blade.php`; Test `tests/Feature/MobileDashboardPagesTest.php`

**Interfaces:** Dữ liệu: `Invoice::create(['project_id','code' => 'HD-T1','invoice_date' => now(),'total_amount' => 1500000])`.

- [ ] **Step 1: Failing tests**
  - `it('renders the mobile invoice list inside the dashboard shell')`: GET `route('projects.invoices.index', $project)` → `toBeDashboardMobilePage()`, chứa `HD-T1`, chứa `route('customers.projects.index', $project->customer_id)`.
  - `it('renders the mobile invoice detail with print-safe chrome')`: GET `route('invoices.show', $invoice)` → `toBeDashboardMobilePage()`, chứa `window.print()` và `route('invoices.pdf', $invoice)`; `toMatch('/<aside[^>]*print:hidden/')`, topbar (`<header` của `<x-topbar>`) và `<footer` của layout có `print:hidden`.
- [ ] **Step 2:** Chạy — Expected: FAIL.
- [ ] **Step 3:** Chuyển 2 view (spec 1–10; back dẫn về danh sách dự án/hóa đơn tương ứng). Thanh In/PDF của `mobile_show` giữ, đặt `fixed inset-x-0 bottom-0` + safe-area, thêm `print:hidden`, nội dung có `pb` đủ để không bị che. Trong `layouts/dashboard.blade.php` thêm `print:hidden` cho overlay sidebar, `<x-sidebar>` (thêm vào class của `<aside>` trong `components/sidebar.blade.php`), `<x-topbar>` (`components/topbar.blade.php`), `<footer>`; `<main>` thêm `print:overflow-visible`.
- [ ] **Step 4:** pest + build — pass.
- [ ] **Step 5:** Trình duyệt 375px: 2 trang không cuộn ngang; thanh In/PDF không che dòng cuối; desktop 1280px các trang khác không đổi.
- [ ] **Step 6:** Commit `refactor: render mobile invoice list and detail inside the dashboard layout`.

### Task 5: Hóa đơn — tạo và sửa

**Files:** Modify `resources/views/invoices/mobile_create.blade.php`, `resources/views/invoices/mobile_edit.blade.php`; Test `tests/Feature/MobileDashboardPagesTest.php`

- [ ] **Step 1: Failing tests** cho `route('projects.invoices.create', $project)` và `route('invoices.edit', $invoice)`: `toBeDashboardMobilePage()`; form action đúng (`route('projects.invoices.store', $project)` / `route('invoices.update', $invoice)`); `toContain('@submit="isLoading = true"')` hoặc `x-on:submit` tương đương; không chứa các chữ `General Information`, `Invoice Items`, `Add Item`, `Total Amount`.
- [ ] **Step 2:** Chạy — Expected: FAIL.
- [ ] **Step 3:** Chuyển 2 view (spec 1–10). Giữ nguyên tên trường, mảng dòng vật liệu và `<script>`; `.sticky-actions` giữ trong `<style>` của content, cố định đáy + safe-area; Việt hóa chữ (kể cả chuỗi lấy từ `__()` hiện ra tiếng Anh — thay bằng chữ Việt trực tiếp hoặc thêm khóa vào `lang/vi.json`).
- [ ] **Step 4:** pest + build — pass.
- [ ] **Step 5:** Trình duyệt 375px: thêm/xóa một dòng vật liệu, tổng tiền cập nhật, thanh lưu không che dòng cuối (không gửi form).
- [ ] **Step 6:** Commit `refactor: render mobile invoice create and edit inside the dashboard layout`.

### Task 6: Import Excel — 3 bước

**Files:** Modify `resources/views/import-invoice-mobile.blade.php`, `resources/views/import-sheet-selection-mobile.blade.php`, `resources/views/import-result-mobile.blade.php`; Test `tests/Feature/MobileDashboardPagesTest.php`

**Interfaces:** Bước 2, 3 render trực tiếp: `view('import-sheet-selection-mobile', ['sheetNames' => ['Sheet1','Sheet2'], 'storedFilePath' => 'imports/x.xlsx', 'projectId' => null])`; `view('import-result-mobile', ['khachHang' => 'Thầu A', 'diaChi' => '', 'dienThoai' => '', 'items' => [['stt' => 1, 'ngay_thang' => '01/11/2026', 'ten_vat_lieu' => 'Xi măng', 'don_vi_tinh' => 'bao', 'so_luong' => 10, 'don_gia' => 90000, 'thanh_tien' => 900000]], 'advancePayments' => [], 'tongTien' => 900000, 'tongTienUng' => 0, 'tongTienChu' => 'Chín trăm nghìn đồng', 'ngayCuoi' => '01/11/2026', 'projectId' => null])` — cả hai sau `actingAsAdmin()`.

- [ ] **Step 1: Failing tests** — 3 ca: GET `route('invoice.index')`; render 2 view trên. Mỗi ca `toBeDashboardMobilePage()` và chứa `@submit="isLoading = true"`; bước 1 chứa `route('invoice.import')`, bước 2 chứa `Sheet2` và chữ "Tiếp tục", bước 3 chứa `Xi măng` và `route('invoice.save')`; không chứa `dark_mode`.
- [ ] **Step 2:** Chạy — Expected: FAIL.
- [ ] **Step 3:** Chuyển 3 view (spec 1–13): bỏ logo CMS Inc., bottom nav, footer, nút sáng/tối, overlay riêng; `material-icons-outlined` → `material-symbols-outlined`; `sidebar-light/dark` → `container-light/dark`; `--primary: #007BFF`; giữ `.custom-radio` trong `<style>`; `x-data` body → div bọc, `loading` → dùng `isLoading` của layout; tiêu đề tiếng Việt ("Nhập hóa đơn Excel", "Chọn sheet cần nhập", "Xem trước dữ liệu"; nút "Tiếp tục").
- [ ] **Step 4:** pest + build — pass.
- [ ] **Step 5:** Trình duyệt 375px: bước 1 thật (chọn file hiển thị tên, không gửi); bước 2, 3 qua file HTML render tạm trong `public/` (xóa sau khi xem) — không cuộn ngang, sidebar mở được.
- [ ] **Step 6:** Commit `refactor: render mobile Excel import steps inside the dashboard layout`.

### Task 7: Dọn dẹp và kiểm tra tổng

**Files:** Delete `resources/views/components/mobile-sidebar.blade.php`, `tailwind.import.config.js`, `resources/css/import.css`; Modify `vite.config.js` (bỏ entry `import.css`); Modify `tests/Feature/MobileSidebarTest.php` (đổi thành kiểm sidebar dashboard: `toBeDashboardMobilePage()` cho customers, import, sheet selection — hoặc xóa nếu đã trùng với `MobileDashboardPagesTest`).

Giữ `mobile.css`/`mobile-compact.css` và config của chúng (còn dùng bởi `auth/login`, `components/customer/app`).

- [ ] **Step 1:** `grep -rn "mobile-sidebar\|import.css\|tailwind.import" resources vite.config.js` — Expected: không còn tham chiếu ngoài các file sắp xóa.
- [ ] **Step 2:** Xóa/sửa các file trên.
- [ ] **Step 3:** `vendor/bin/pest`, `vendor/bin/phpstan analyse --memory-limit=1G`, `npm run build` — Expected: all pass, `[OK] No errors`, build sạch.
- [ ] **Step 4:** Trình duyệt 375px lần lượt 9 trang + Tổng quan: cùng topbar/sidebar/màu; chụp ảnh 2–3 trang tiêu biểu.
- [ ] **Step 5:** Commit `chore: drop the import theme and standalone mobile sidebar`.
