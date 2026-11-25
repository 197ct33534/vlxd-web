# SYSTEM_CAPABILITIES.md

Bản đồ tính năng cho **vlxd-web**. Xem [AGENTS.md](./AGENTS.md) để biết quy tắc dữ liệu và điểm vào khi sửa code.

Đối chiếu với route và migration đã commit ngày 25/11/2026. Xem [routes/web.php](routes/web.php) và [routes/api.php](routes/api.php) để xác nhận phạm vi truy cập hiện tại.

---

## Cross-cutting

| Topic | Detail |
|-------|--------|
| **Staff auth** | Session cookie; `users.email` + password. Middleware: `auth`. Roles on `users.role`: `admin`, `employee` (default), **`driver`** (`User::isDriver()`). |
| **Portal auth** | Session key `portal_customer_id` → `customers.id`; middleware alias **`portal`** (`PortalAuth`). Login: phone match (normalized digits) against `customers.phone`. Routes under prefix **`/m`**, name prefix **`portal.*`**. |
| **Roles middleware** | Alias **`role`** (`EnsureRole`) — **`driver.*`** (`role:driver,admin`); **`admin.orders.*`** (`role:admin`). |
| **Project totals** | `projects.total_invoice`, `total_paid`, `total_debt` recalculated by `ProjectDebtService` after invoice/payment mutations. |
| **Schema drift** | Models/controllers may use column names that differ from migrations (e.g. invoice date fields, `payment_type` enum vs app strings). Xem mục **Quy tắc dữ liệu cần giữ** trong [AGENTS.md](AGENTS.md). |

---

## Quản trị cửa hàng

### Authentication & dashboard

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web (Blade + Vite layout) |
| **Status** | Hoàn thành; dashboard thống kê qua `DashboardController` |

**Flow:** `GET /login` → POST authenticate → redirect trang yêu cầu trước đó hoặc `dashboard`; POST logout.

**Endpoints:** `login`, `authenticate`, `logout`, `GET /dashboard` → `dashboard`. `GET /` là landing công khai.

**Models:** `User` (`users`).

---

### Khách hàng (Customers)

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** List/search customers → create/update/delete. Customer owns many projects.

**Endpoints:** `GET/POST customers.*` — `customers.index`, `customers.store`, `customers.update`, `customers.destroy`.

**Models:** `Customer` (`customers`: name, address, phone, status; model also lists `email` — verify DB).

---

### Công trình (Projects)

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** Under a customer, CRUD projects; track debt aggregates and status `dang_thi_cong` \| `hoan_thanh` \| `tam_ngung`.

**Endpoints:** Nested `customers/{customer}/projects` — `customers.projects.index|store|update|destroy`.

**Models:** `Project` (`projects`).

**Related:** `Project::orders()` — portal/admin orders may reference a project.

---

### Hóa đơn (Invoices) & dòng vật liệu

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** Per project: list/create/edit/delete invoices and line items; totals roll up to project.

**Endpoints:** `projects.invoices.*`, `invoices.show|edit|update|destroy|pdf`, `invoices.pay` và `invoices.move-project`.

**Models:** `Invoice` (`invoices`), `InvoiceItem` (`invoice_items`).

**Hiển thị:** Ngày xuất nằm trên hóa đơn. Ngày giao nằm trên từng dòng vật liệu. Chi tiết desktop và hộp Xem nhanh dùng cùng partial. Hóa đơn có thể chuyển sang công trình cùng khách hàng khi chưa có khoản thu hoặc đơn hàng liên kết.

---

### Thanh toán (Payments)

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Luồng:** Phiếu thu thuộc công trình. Bảng `payment_allocations` phân bổ phiếu thu vào từng hóa đơn. Khi thêm, sửa hoặc xóa phiếu thu, tính lại công nợ công trình.

**Endpoints:** `projects.payments.store`, `payments.update`, `payments.destroy`.

**Models:** `Payment` (`payments`; DB enum `mot_lan`/`tung_dot`).

**Nghiệp vụ:** `markAsPaid` chỉ ghi số tiền còn thiếu vào hóa đơn đích. Form thêm và sửa phiếu thu dùng lịch tiếng Việt, hiển thị `dd/mm/yyyy` và gửi `Y-m-d`.

---

### Lịch sử giá (Price history)

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** Read-only grid of historical unit prices from past invoice lines for a project.

**Endpoints:** `GET projects/{project}/price-history` → `projects.price_history`.

**Models:** Query `invoice_items` ⋈ `invoices` (no dedicated table).

---

### Import hóa đơn Excel

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Luồng:** Tải workbook → chọn công trình → xem trước các sheet → xác nhận ngày xuất và lưu các sheet đã chọn. Mỗi sheet lưu riêng. Hệ thống nhận ra nội dung đã nhập trong cùng công trình và cảnh báo khi hóa đơn cũ có dòng vật liệu và tổng tiền khớp.

**Endpoints:** `invoice.index`, `invoice.import`, `invoice.workbook`, `invoice.workbook.save`. Route `invoice.save` giữ luồng cũ.

**Điểm vào:** `ImportInvoiceController`, `InvoiceImportMatcher` và `resources/views/import-workbook.blade.php`.

---

### Báo cáo ngày (Daily reports)

| Field | Value |
|-------|--------|
| **Actors** | Employee (submit); Admin (approve/reject in UI) |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** Employee submits report + line items (project, product, qty, price) → `pending` → admin approves/rejects. Status: `pending` \| `approved` \| `rejected`.

**Endpoints:** Resource `daily-reports.*`; `daily-reports.approve`, `daily-reports.reject`.

**Models:** `DailyReport`, `DailyReportItem`.

**API (staff session):** `GET /api/latest-price?project_id=&product_name=` → JSON `{ price }` from latest matching `invoice_items` for project (`api.latest-price`).

**Phân quyền:** Route duyệt và từ chối nằm trong nhóm middleware `admin`.

---

### Nhân viên & ứng lương (Employees)

| Field | Value |
|-------|--------|
| **Actors** | Admin (typical); routes not admin-gated in local `web.php` |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** CRUD employees; record salary advances per employee.

**Endpoints:** `employees` resource; `employees.advances.store`, `employees.advances.index`.

**Models:** `Employee`, `SalaryAdvance`.

**Note:** On `origin/main`, employee routes sit inside `middleware admin` — **local tree exposes them to any authenticated user**.

---

## Mobile-First (Portal, PWA, Orders)

### PWA shell

| Field | Value |
|-------|--------|
| **Actors** | Thầu / Thợ / Chủ nhà (contractor persona) |
| **Platform** | Mobile Web, **PWA** (`manifest.webmanifest`, `sw.js`) |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** Installable shell; `start_url` `/m`; caches shell assets; network-first for navigations. Service worker registered from `layouts/mobile-customer`.

**Routes:** `GET /m` → redirect `/m/login`; portal pages under `/m/*`.

**Touchpoints:** `public/manifest.webmanifest`, `public/sw.js`, `public/css/mobile-first.css`, `resources/views/layouts/mobile-customer.blade.php`.

---

### Cửa hàng — tel / Zalo CTA

| Field | Value |
|-------|--------|
| **Actors** | Khách portal |
| **Platform** | Mobile Web |
| **Status** | Hoàn thành (read + UI) **[UPDATED]** |

**Flow:** `StoreInfo::current()` drives header call/Zalo icons and sticky `<x-store-cta>` (`tel:` or `zalo_oa_url` / `zalo.me/{phone}`).

**Models:** `StoreInfo` (`store_info`; columns `zalo_oa_id`, `zalo_oa_url` via migration).

**UI:** `resources/views/components/store-cta.blade.php`.

**Admin UI:** `GET/PUT /admin/store-info` (`admin.store-info.*`, `role:admin`) — `Admin\StoreInfoController`.

---

### Portal khách — đăng nhập & trang chủ

| Field | Value |
|-------|--------|
| **Actors** | Khách có `customers.phone` (thầu/thợ/chủ nhà) |
| **Platform** | Mobile Web, PWA-oriented layout |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** `GET /m/login` → POST phone → session `portal_customer_id` → home shows **tổng nợ**, **công trình**, **5 đơn gần đây**, CTA đặt hàng.

**Controllers:** `Portal\AuthController`, `Portal\HomeController`.

**Views:** `portal/login`, `portal/home`, `layouts/mobile-customer`, `portal/partials/bottom-nav`.

**Endpoints (name → path):**  
`portal.login` → `GET/POST /m/login`; `portal.logout` → `POST /m/logout`; `portal.home` → `GET /m/` (middleware **`portal`**).

**Middleware:** `portal` on protected portal group.

---

### Đơn hàng — Portal (khách đặt)

| Field | Value |
|-------|--------|
| **Actors** | Khách portal |
| **Platform** | Mobile Web |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** 3-step create (project → lines → confirm) → `pending_confirm` → detail/history; optional **reorder** query; **latest price** from invoice history per project.

**Controller:** `Portal\OrderController`.

**Views:** `portal/orders/create`, `index`, `show`.

**Endpoints:**  
`portal.orders.index` → `GET /m/orders`;  
`portal.orders.create` → `GET /m/orders/create`;  
`portal.orders.store` → `POST /m/orders`;  
`portal.orders.show` → `GET /m/orders/{order}`;  
`portal.orders.latest-price` → `GET /m/api/latest-price?project_id=&product_name=` (JSON `{ price, unit, product_name, source, priced_at }`);  
`portal.orders.suggest` → `GET /m/api/suggest-products?q=&project_id=` (autocomplete từ invoice/order history).

**Models:** `Order`, `OrderItem`.

**Business rule:** New orders created with `status` **`pending_confirm`**; line totals computed on submit.

---

### Đơn hàng — Admin (shop xác nhận)

| Field | Value |
|-------|--------|
| **Actors** | Admin (`role:admin`) |
| **Platform** | Desktop Web (Blade dashboard) |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** List/filter by status → detail → sửa giá/SL → **confirm** → gắn CT / gán tài xế → **Xuất hóa đơn** (tạo `invoices` + `invoice_items`, cập nhật `projects.total_*`, set `orders.invoice_id`) / **cancel**.

**Controller:** `Admin\OrderController`.

**Views:** `admin/orders/index`, `show`. Nav: sidebar **Đơn mobile** (admin only).

**Endpoints (middleware `auth` + `role:admin`):**  
`admin.orders.index` → `GET /admin/orders`;  
`admin.orders.show` → `GET /admin/orders/{order}`;  
`admin.orders.confirm` → `POST /admin/orders/{order}/confirm`;  
`admin.orders.items.update` → `PUT /admin/orders/{order}/items`;  
`admin.orders.project.update` → `PUT /admin/orders/{order}/project`;  
`admin.orders.assign-driver` → `POST /admin/orders/{order}/assign-driver`;  
`admin.orders.cancel` → `POST /admin/orders/{order}/cancel`;  
`admin.orders.export-invoice` → `POST /admin/orders/{order}/export-invoice`.

**Tables:** `orders.invoice_id` → `invoices.id` (migration `2026_03_26_100200_add_invoice_id_to_orders_table`).

---

### Giao hàng — Tài xế

| Field | Value |
|-------|--------|
| **Actors** | User `role=driver` (admin may access UI) |
| **Platform** | Mobile-friendly Web (staff session) |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** Assigned orders (`confirmed` \| `delivering`) → **start** (`delivering`) → **complete** with optional photo + GPS → `delivered`.

**Controller:** `Driver\DeliveryController`.

**Views:** `driver/index`, `driver/show`.

**Endpoints (middleware `auth`, **`role:driver,admin`**):**  
`driver.index` → `GET /driver`;  
`driver.show` → `GET /driver/orders/{order}`;  
`driver.start` → `POST /driver/orders/{order}/start`;  
`driver.complete` → `POST /driver/orders/{order}/complete`.

**Order lifecycle (shared):**  
`draft` → `pending_confirm` → `confirmed` → `delivering` → `delivered` \| `cancelled`.

**Tables:** `orders`, `order_items` (migration `2026_03_26_100100_create_orders_table.php`).

---

### Zalo ZNS & Mini App

| Field | Value |
|-------|--------|
| **Actors** | Khách (notifications) |
| **Platform** | Zalo OA / ZNS (kênh); Mini App **not built** |
| **Status** | ZNS **stub** Hoàn thành; Mini App Chưa triển khai **[UPDATED]** |

**Flow:** On confirm / start delivery / complete, `ZaloNotifier::notifyOrderStatus()` posts to ZNS when `ZALO_OA_ACCESS_TOKEN` + template env set; otherwise logs only.

**Touchpoints:** `app/Services/ZaloNotifier.php`, `config/zalo.php`; store deep links via `StoreInfo` / portal CTA.

**Mini App skeleton:** `public/zalo-miniapp/` + [docs/ZALO_MINIAPP.md](./docs/ZALO_MINIAPP.md) — splash redirect vào `/m`. Chưa OAuth Zalo user mapping.

**Gap:** production ZNS credentials required for live customer sends; Mini App OAuth chưa làm.

---

## API

| Endpoint | Auth | Status |
|----------|------|--------|
| `GET /api/user` | Sanctum | Hoàn thành (returns authenticated user) |
| `GET /api/v1/orders` | Sanctum | Hoàn thành **[UPDATED]** — admin: all orders; driver: `driver_id` = self; optional `?status=` |
| `GET /api/v1/orders/{order}` | Sanctum | Hoàn thành **[UPDATED]** — admin or assigned driver |
| `GET /api/latest-price` | Staff `auth` session | Hoàn thành (web route `api.latest-price`) |
| `GET /m/api/latest-price` | Portal `portal` session | Hoàn thành **[UPDATED]** — `portal.orders.latest-price` |

**Controller:** `Api\OrderController` (`routes/api.php`).

**Auth API:** `POST /api/v1/auth/token` (email/password → Sanctum plain text token, admin/driver only); `DELETE /api/v1/auth/token` (revoke current).

**Driver actions API:** `POST /api/v1/orders/{order}/start`, `POST /api/v1/orders/{order}/complete`.

### Calculator khối lượng (portal)

| Field | Value |
|-------|--------|
| **Actors** | Thầu / thợ (portal) |
| **Platform** | Mobile Web / PWA |
| **Status** | Hoàn thành **[UPDATED]** |

**Flow:** `/m/calculator` — ước bê tông / gạch / vữa tô → đẩy dòng vào `portal.orders.create` (sessionStorage).

**Endpoint:** `portal.calculator` → `GET /m/calculator`.

### ShopNotifier (Telegram fallback)

| Field | Value |
|-------|--------|
| **Actors** | Shop nội bộ |
| **Platform** | Telegram Bot API |
| **Status** | Hoàn thành (optional config) **[UPDATED]** |

**Flow:** Đơn mới từ portal → Telegram; confirm/start/complete cũng ping nội bộ. Missing `TELEGRAM_*` → log only.

**Touchpoints:** `app/Services/ShopNotifier.php`, `config/telegram.php`.

---

## Bảng giá, kho và báo cáo quản trị

- Landing công khai ở `/`. Tải báo giá qua `quotation.download`.
- Danh sách bảng giá `material-prices.index` dành cho nhân viên đã đăng nhập. Thêm, sửa, xóa và đồng bộ từ lịch sử nằm trong nhóm admin.
- Cấu hình tên, địa chỉ, số điện thoại và tài khoản cửa hàng qua `store-settings.edit/update` và `StoreInfo`.
- Tồn kho và phiếu nhập ở `admin.inventory.*`, `admin.stock-receipts.*` và `StockService`.
- Sổ nợ và đối chiếu ở `admin.customers.debt`, `admin.projects.reconciliation-pdf`. Báo cáo quản trị và xuất dữ liệu ở `admin.reports.*`.

## Entity quick reference

```
Customer ──1:*──► Project ──1:*──► Invoice ──1:*──► InvoiceItem
                      │
                      ├──1:*──► Payment ──1:*──► PaymentAllocation ──*──1──► Invoice
                      └──1:*──► Order ──1:*──► OrderItem

User ──1:*──► DailyReport ──1:*──► DailyReportItem ──*──1──► Project
User (driver_id, confirmed_by) ──*──1──► Order

Employee ──1:*──► SalaryAdvance
StoreInfo (singleton row; Zalo OA fields + tel for portal CTA)
```

---

## Change log (doc)

| Date | Change |
|------|--------|
| 2026-03-26 | Initial document from local `main` + uncommitted Mobile-First (portal, orders, PWA, StoreInfo/Zalo). |
| 2026-10-15 | **[UPDATED]** Mobile-First wired: portal `/m`, admin/driver orders, PWA, ZaloNotifier stub, Sanctum `/api/v1/orders`. |
| 2026-11-25 | Cập nhật nhập nhiều sheet, ngày hóa đơn, phân bổ thanh toán, chuyển công trình và các route quản trị. |
