# SYSTEM_CAPABILITIES.md

Machine-readable capability map for **vlxd-web** (Laravel 10, MySQL). Complements [AGENTS.md](./AGENTS.md).

**Snapshot:** 2026-10-15 — local `main` may still be **behind `origin/main`**; working tree includes **wired Mobile-First** (portal `/m`, admin/driver orders, PWA, Sanctum orders API) and **does not include** several modules still on `origin/main` (see [§ Remote-only on origin/main](#remote-only-on-originmain-not-in-current-working-tree)).

---

## Cross-cutting

| Topic | Detail |
|-------|--------|
| **Staff auth** | Session cookie; `users.email` + password. Middleware: `auth`. Roles on `users.role`: `admin`, `employee` (default), **`driver`** (`User::isDriver()`). |
| **Portal auth** | Session key `portal_customer_id` → `customers.id`; middleware alias **`portal`** (`PortalAuth`). Login: phone match (normalized digits) against `customers.phone`. Routes under prefix **`/m`**, name prefix **`portal.*`**. |
| **Roles middleware** | Alias **`role`** (`EnsureRole`) — **`driver.*`** (`role:driver,admin`); **`admin.orders.*`** (`role:admin`). |
| **Project totals** | `projects.total_invoice`, `total_paid`, `total_debt` updated from invoice/payment flows in `InvoiceController` (verify after changes). |
| **Schema drift** | Models/controllers may use column names that differ from migrations (e.g. invoice date fields, `payment_type` enum vs app strings). See AGENTS.md §6. |

---

## Staff back-office (Desktop Web)

### Authentication & dashboard

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web (Blade + Vite layout) |
| **Status** | Hoàn thành (core); dashboard is static view |

**Flow:** `GET /login` → POST authenticate → redirect `customers.index`; POST logout.

**Endpoints:** `login`, `authenticate`, `logout`, `GET /` → `dashboard`.

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

**Endpoints:** `projects.invoices.*`, `invoices.show|edit|update|destroy`, `invoices.pay` (mark paid).

**Models:** `Invoice` (`invoices`), `InvoiceItem` (`invoice_items`).

**Note:** `origin/main` adds `invoices.pdf` — **not in local `web.php`**.

---

### Thanh toán (Payments)

| Field | Value |
|-------|--------|
| **Actors** | Admin, Employee |
| **Platform** | Desktop Web |
| **Status** | Hoàn thành |

**Flow:** Payments attach to **project** (not invoice FK). Create/update/delete adjusts `total_paid` / `total_debt`.

**Endpoints:** `projects.payments.store`, `payments.update`, `payments.destroy`.

**Models:** `Payment` (`payments`; DB enum `mot_lan`/`tung_dot`).

**Business rule:** Payment is project-scoped; invoice `markAsPaid` is separate shortcut on invoice resource.

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

**Flow:** Upload Excel → preview/select sheet → save creates invoice + items (PhpSpreadsheet in controller). `saveSimple` exists in controller but **no route**.

**Endpoints:** `invoice.index`, `invoice.import`, `invoice.save`.

**Controller:** `ImportInvoiceController`.

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

**Gap:** Approve/reject not wrapped in `admin` middleware (role check mainly in list UI).

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

## Remote-only on origin/main (not in current working tree)

Local branch + uncommitted work **removed or never had** the following capabilities that still exist on **`origin/main`** (~4 commits ahead). Agents merging/pulling should expect them to return unless intentionally dropped.

| Module | Actors | Platform | Endpoints / notes | Status on remote |
|--------|--------|----------|-------------------|------------------|
| **Landing & báo giá công khai** | Khách mua lẻ | Mobile/Desktop Web | `/` landing, `quotation.download`, `landing.quote-request` | Hoàn thành on remote |
| **Bảng giá vật liệu** | Admin | Desktop | `material-prices.*`, sync from history | Hoàn thành on remote |
| **Cài đặt cửa hàng** | Admin | Desktop | `store-settings.edit/update` | Hoàn thành on remote |
| **Báo cáo admin** | Admin | Desktop | `admin.reports.*`, Excel/PDF exports | Hoàn thành on remote |
| **Dashboard thống kê** | Staff | Desktop | `/dashboard` → `DashboardController` | Hoàn thành on remote |
| **Tất cả công trình** | Staff | Desktop | `projects.index` (global list) | Hoàn thành on remote |
| **Hóa đơn PDF** | Staff | Desktop | `invoices.pdf` | Hoàn thành on remote |
| **Admin middleware** | Admin | — | `EnsureUserIsAdmin`, `admin` route group for employees/reports/settings | Hoàn thành on remote |
| **i18n** | — | — | `lang/vi.json` | Present on remote |

---

## Entity quick reference

```
Customer ──1:*──► Project ──1:*──► Invoice ──1:*──► InvoiceItem
                      │
                      ├──1:*──► Payment
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
