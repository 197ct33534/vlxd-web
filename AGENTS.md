# AGENTS.md — Bản đồ dự án vlxd-web

> Tài liệu định hướng cho AI agent. Đọc file này trước khi sửa code.

## 1. Tổng quan

| Mục | Chi tiết |
|-----|----------|
| **Tên** | `vlxd-web` |
| **Domain** | Quản lý cửa hàng **VLXD** (vật liệu xây dựng) — khách hàng, công trình, hóa đơn, thanh toán, báo cáo ngày, nhân viên |
| **Stack** | Laravel 10, PHP ^8.1, Blade + Vite, MySQL |
| **Auth** | Session (`middleware auth`); role `admin` / `employee` trên `users.role` |
| **Package chính** | `maatwebsite/excel` (cài sẵn), import thực tế dùng **PhpSpreadsheet** trong controller |
| **README** | Gần như trống — dùng file này làm nguồn truth cho agent |
| **Capabilities** | Chi tiết module/endpoint/trạng thái: [SYSTEM_CAPABILITIES.md](./SYSTEM_CAPABILITIES.md) |

## 2. Luồng nghiệp vụ cốt lõi

```
Customer ──1:*──► Project ──1:*──► Invoice ──1:*──► InvoiceItem
                      │
                      └──1:*──► Payment   (thanh toán theo công trình, không FK invoice)

User ──1:*──► DailyReport ──1:*──► DailyReportItem ──*──1──► Project

Employee ──1:*──► SalaryAdvance
```

1. Tạo **Khách hàng** → tạo **Công trình** (project).
2. Tạo **Hóa đơn** + dòng vật liệu (`invoice_items`), hoặc **Import Excel**.
3. Ghi **Thanh toán** (`payments`) gắn `project_id`; cập nhật `total_invoice` / `total_paid` / `total_debt`.
4. Nhân viên gửi **Báo cáo ngày** → admin duyệt/từ chối.
5. **Lịch sử giá** = query từ `invoice_items` (không có bảng riêng).

## 3. Đầu mục module (điểm vào code)

### 3.1 Auth & Dashboard
- Routes: `/login`, `/logout`, `/` (`dashboard`)
- Controller: `app/Http/Controllers/AuthController.php`
- View: `resources/views/auth/login.blade.php`, `resources/views/dashboard.blade.php`

### 3.2 Khách hàng (Customers)
- Routes: `customers.*` — prefix `/customers`
- Controller: `CustomerController` — `index`, `store`, `update`, `destroy`
- Model: `app/Models/Customer.php`
- Views: `resources/views/customer/`, `resources/views/components/customer/`

### 3.3 Công trình (Projects)
- Routes: nested `customers/{customer}/projects` → `customers.projects.*`
- Controller: `ProjectController`
- Model: `app/Models/Project.php`
- Status DB: `dang_thi_cong` | `hoan_thanh` | `tam_ngung`
- Views: `resources/views/projects/`

### 3.4 Hóa đơn & Thanh toán (Invoices / Payments)
- Routes: `projects.invoices.*`, `invoices.*`, `projects.payments.*`, `payments.*`, `invoices.pay`
- Controller: `InvoiceController` (CRUD invoice + payment + `markAsPaid`)
- Models: `Invoice`, `InvoiceItem`, `Payment`
- Views: `resources/views/invoices/`

### 3.5 Import hóa đơn Excel
- Routes: `/import-invoice` → `invoice.index` / `invoice.import` / `invoice.save`
- Controller: `ImportInvoiceController` (logic lớn ~20KB; preview sheet → save)
- Views: `import-invoice.blade.php`, `import-result.blade.php`, `import-sheet-selection.blade.php`

### 3.6 Lịch sử giá (Price history)
- Route: `projects/{project}/price-history` → `projects.price_history`
- Controller: `PriceHistoryController` — join `invoice_items` + `invoices`
- View: `resources/views/projects/price_history.blade.php`

### 3.7 Báo cáo ngày (Daily reports)
- Routes: `daily-reports.*` + `approve` / `reject` + `api.latest-price`
- Controller: `DailyReportController`
- Models: `DailyReport`, `DailyReportItem`
- Status: `pending` | `approved` | `rejected`
- Views: `resources/views/daily_reports/`
- **Lưu ý:** phân quyền admin chủ yếu ở UI/controller list; chưa có middleware admin-only cho approve/reject

### 3.8 Nhân viên & Ứng lương (Employees)
- Routes: `employees` resource + `employees.advances.*`
- Controller: `EmployeeController`
- Models: `Employee`, `SalaryAdvance`
- Views: `resources/views/employees/`

### 3.9 Store info
- Model: `app/Models/StoreInfo.php` (+ `zalo_oa_id`, `zalo_oa_url`)
- Dùng cho CTA gọi/Zalo trên portal; seeder `StoreInfoSeeder`

### 3.10 Portal khách + Orders + Driver (Mobile-First)
- Portal: `/m` — auth SĐT, home công nợ, đặt hàng 3 bước, lịch sử đơn
- Admin đơn: `/admin/orders` — xác nhận / hủy / gán tài xế
- Driver: `/driver` — bắt đầu giao / hoàn thành (+ GPS/ảnh)
- Models: `Order`, `OrderItem`; Zalo stub: `app/Services/ZaloNotifier.php`

### 3.11 API
- `routes/api.php` — Sanctum `GET /api/user` + `GET /api/v1/orders` (admin/driver)
- Web: `api.latest-price`, `portal.orders.latest-price`

## 4. Cấu trúc thư mục quan trọng

```
app/
  Http/Controllers/     # Toàn bộ nghiệp vụ (không có Services layer)
  Models/               # 10 Eloquent models
  Providers/
database/migrations/    # Schema domain từ 2025-10 + role/employees/reports 2025-12
resources/views/        # Blade UI theo module
routes/web.php          # Một file routes chính (protected bởi auth)
```

**Không có:** `app/Services`, `app/Jobs`, `app/Exports`, `app/Imports` (Maatwebsite classes).

## 5. Models nhanh

| Model | Bảng | Quan hệ chính |
|-------|------|----------------|
| User | users | role; `isAdmin()` |
| Customer | customers | hasMany Project |
| Project | projects | belongsTo Customer; hasMany Invoice, Payment |
| Invoice | invoices | belongsTo Project; hasMany InvoiceItem |
| InvoiceItem | invoice_items | belongsTo Invoice |
| Payment | payments | belongsTo Project |
| Employee | employees | hasMany SalaryAdvance |
| SalaryAdvance | salary_advances | belongsTo Employee |
| DailyReport | daily_reports | belongsTo User; hasMany DailyReportItem |
| DailyReportItem | daily_report_items | belongsTo DailyReport, Project |

## 6. Cảnh báo kỹ thuật (đọc trước khi sửa)

1. **Schema drift** — `$fillable` / tên cột model đôi khi lệch DB/controller (vd. Invoice: `date` vs `invoice_date`, `total_amount_text` vs `total_text`).
2. **Customer.email** — có trong model/validation, migration gốc dùng `note` (không có `email`).
3. **Payment.payment_type** — migration enum `mot_lan`/`tung_dot`; code có thể ghi `manual`/`ung_truoc`.
4. **Invoice::payments()** — quan hệ có trong model nhưng `payments` không có `invoice_id`.
5. **ImportInvoiceController::saveSimple** — method tồn tại, **không** gắn route.
6. Branch `main` có thể **behind** `origin/main`; branch `upload-imagee` gợi ý feature upload ảnh đang làm riêng.

## 7. Checklist khi agent làm việc

1. Đọc `routes/web.php` để xác định entry point module.
2. Sửa controller + model + migration/view cùng module; tránh thêm Service layer trừ khi được yêu cầu.
3. Giữ pattern Blade hiện tại (`layouts/`, `components/sidebar`).
4. Không commit secret / `.env`.
5. Sau khi đụng Invoice/Payment: kiểm tra cập nhật `projects.total_*`.

## 8. Entry files ưu tiên

| Mục đích | File |
|----------|------|
| Routes | `routes/web.php` |
| Import Excel | `app/Http/Controllers/ImportInvoiceController.php` |
| Hóa đơn + tiền | `app/Http/Controllers/InvoiceController.php` |
| Nav/sidebar | `resources/views/components/sidebar.blade.php` |
| Schema | `database/migrations/2025_10_14_*` và `2025_12_22_*` |
| Mobile portal | `routes/web.php` (`portal.*`, `m.*`), `layouts/mobile-customer.blade.php` |
| Orders | `app/Http/Controllers/Portal/OrderController.php`, `Admin/OrderController.php` |
| Driver / API | `app/Http/Controllers/Driver/DeliveryController.php`, `routes/api.php` |
| PWA | `public/manifest.webmanifest`, `public/sw.js` |

## 9. Mobile-First (chiến lược đã chốt)

**Persona:** thầu / thợ / chủ nhà trên smartphone tại công trường.
**Giá:** khách quen lấy giá lần trước theo công trình; đơn mới → shop xác nhận.
**Kiến trúc:** Responsive Web + PWA trên Laravel; Zalo = kênh (OA/ZNS/deep-link); Mini App / native chỉ Phase 3.

| Phase | Thời gian | Nội dung |
|-------|-----------|----------|
| 1 | 1–2 tuần | Layout mobile, touch CTA, tel/Zalo, PWA shell, portal xem nợ |
| 2 | ~1 tháng | Đặt hàng 3 bước, `orders`/`order_items`, lịch sử + trạng thái |
| 3 | Dài hạn | Zalo ZNS, Sanctum API, màn tài xế |

**Luồng đơn:** `draft` → `pending_confirm` → `confirmed` → `delivering` → `delivered` / `cancelled`

**Portal auth:** đăng nhập bằng SĐT khớp `customers.phone` (session `portal_customer_id`).

Xem thêm canvas: `canvases/mobile-first-vlxd-roadmap.canvas.tsx`
