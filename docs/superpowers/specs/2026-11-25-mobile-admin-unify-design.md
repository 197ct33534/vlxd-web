# Đồng bộ giao diện mobile phần quản trị

Ngày: 2026-11-25 · Trạng thái: chờ duyệt (đã qua reviewer đọc thử)

## Mục đích

Chủ shop thấy giao diện mobile phần quản trị "không đồng bộ chút nào": mỗi màn một kiểu
header, một màu xanh, có màn có thanh điều hướng dưới, có màn không, có màn tiếng Anh và
logo "CMS Inc.". Mục tiêu: mở bất kỳ màn quản trị nào trên điện thoại đều thấy **cùng một
khung** — khung của màn **Tổng quan** (`layouts.dashboard`), chủ shop đã chọn làm chuẩn.

## Hiện trạng

Phần quản trị trên mobile đang trộn ba kiểu khung:

1. `layouts.dashboard` — topbar (`<x-topbar>`, tiêu đề lấy từ `@section('page_heading')`),
   sidebar trượt (`<x-sidebar>` + store Alpine `layout`), `<x-toast>`, `<x-loading>`, theme
   `tailwind.admin.config.js` (`primary #007BFF`, font Inter, bo góc DEFAULT .5rem / lg .75rem /
   xl 1rem). Đã dùng bởi: Tổng quan, Tất cả dự án, Báo cáo, Sổ nợ, Đơn mobile, Kho, Nhân viên,
   **và `material_prices/mobile_index`** (view mobile riêng nhưng `@extends('layouts.dashboard')`
   — đây là mẫu của hướng được chọn).
2. **9 view mobile độc lập** (file HTML đầy đủ, tự có `<head>`, header, theme), do controller
   trả về khi `Jenssegers\Agent\Agent::isMobile()`:

   | View | Controller | CSS entry hiện tại | Ghi chú |
   |---|---|---|---|
   | `customer/mobile` | CustomerController@index | mobile-compact | `x-data` trên `<body>` (modal thêm/sửa/xóa), FAB "thêm khách", bottom nav tự chế (Trang chủ/Khách hàng/+/Hóa đơn/Cài đặt), `<x-mobile-sidebar>` |
   | `projects/mobile_index` | ProjectController@index | mobile | header có nút back, modal, 1 `<script>` |
   | `invoices/mobile_index` | InvoiceController@index | mobile-compact | back, modal |
   | `invoices/mobile_show` | InvoiceController@show | mobile-compact | `x-data` trên body, back, footer |
   | `invoices/mobile_create` | InvoiceController@create | mobile | back, thanh `.sticky-actions`, 1 `<script>`, chữ tiếng Anh ("General Information", "Invoice Items"…) |
   | `invoices/mobile_edit` | InvoiceController@edit | mobile | như create, có footer |
   | `import-invoice-mobile` | ImportInvoiceController@index | import | `x-data` trên body, logo "CMS Inc.", bottom nav tiếng Anh (Home/Invoices/Clients/Settings), **icon `material-icons-outlined`** (font khác, 13 chỗ), `<x-mobile-sidebar>` |
   | `import-sheet-selection-mobile` | ImportInvoiceController@import | import | như trên + CSS `.custom-radio`, header/nav `z-50`, tiếng Anh ("Select Sheet to Import", "Continue Import") |
   | `import-result-mobile` | ImportInvoiceController@import (bước preview) | import | `x-data` trên body, back, footer, tiếng Anh |

   Ba màu xanh khác nhau (`#007BFF`, `#2a8ff4`, `#2563eb`), hai bộ bo góc.
3. Portal khách `/m` và tài xế `/driver` — `layouts.mobile-customer`, font Be Vietnam Pro,
   `primary #007BFF`.

## Mục tiêu

- 9 view ở bảng trên **`@extends('layouts.dashboard')`**: dùng topbar, sidebar, toast, loading,
  theme admin của dashboard. Tiêu đề trang qua `@section('page_heading')` và `@section('title')`.
- Một màu chủ đạo `#007BFF`, font Inter, bộ bo góc của admin — đến từ `admin.css`.
- Toàn bộ chữ hiển thị là **tiếng Việt**; không còn "CMS Inc."
- Không còn thanh điều hướng dưới tự chế, header tự chế, logo/nút menu giả trong các view này.
  Menu duy nhất là sidebar của dashboard.
- Giữ nguyên **nội dung và hành vi** mobile đã có: thẻ khách hàng có nút Dự án/Sửa/Xóa, modal
  thêm/sửa/xóa, form hóa đơn dạng thẻ, 3 bước import, nút back về trang trước.

## Không làm (đợt này)

- Không gộp view mobile vào view desktop (hướng A — để sau, từng trang).
- Không đụng portal `/m`, tài xế `/driver`, landing, trang đăng nhập.
- Không đổi controller logic, route, dữ liệu, validation. Chỉ đổi phần trình bày.
- Không làm lại thiết kế dashboard.

## Chống mục tiêu (coi là thất bại dù "chạy được")

- Mất chức năng mobile đang có (modal không mở, form không gửi được, mất trường, import đứt bước).
- Desktop bị ảnh hưởng.
- Chồng lớp sai: hai lớp loading, nút cố định bị topbar/thanh khác che, cuộn trang hỏng.
- Icon vỡ (ô vuông/chữ thô) do đổi font icon.

## Các hướng đã cân nhắc

- **A. Gộp một view mỗi trang** (bỏ dò User-Agent, view desktop responsive). Bền nhất, nhưng phải
  viết lại UX mobile đã có và rủi ro đụng desktop. Hoãn.
- **B. Giữ view mobile riêng, cho chạy trong `layouts.dashboard`** — **được chọn**. Nhanh, không
  đụng desktop, giữ UX mobile; đánh đổi là mỗi trang vẫn hai file.
- **C. Chỉ gom màu/font về một config.** Loại: header, menu, thanh dưới vẫn khác — không giải
  quyết điều chủ shop thấy.

## Cách làm (đã quyết)

Với mỗi view trong bảng:

1. Bỏ `<!DOCTYPE>/<html>/<head>/<body>`, `@vite([...])`, link font riêng, `<style>` trùng với
   dashboard (`body{font-family}`, `[x-cloak]`). Bọc bằng `@extends('layouts.dashboard')`,
   `@section('title')`, `@section('page_heading')`, `@section('content')`.
2. Bỏ header tự chế, bottom nav tự chế, footer tự chế, nút menu giả, `<x-mobile-sidebar>`,
   overlay loading riêng của trang (dashboard đã có `<x-loading>` và tự bật khi bấm link).
3. **Nút back** của các trang có back (projects, invoices/*, import-result) giữ lại dưới dạng một
   dòng đầu nội dung (`← Quay lại` + tiêu đề), giống `invoices.create` desktop đang làm
   ("Quay lại · Tạo hóa đơn"), vì topbar dashboard không có nút back.
4. `x-data` đang đặt trên `<body>` chuyển sang một `<div>` bọc ngoài cùng của `@section('content')`
   (giữ nguyên nội dung object, kể cả handler `@click` bắt link). Modal `fixed` nằm trong div này.
5. Đổi class màu theo tên đã có trong admin theme: `primary` giữ tên (giá trị thành `#007BFF`),
   `background-light/dark`, `container-*`, `border-*`, `text-*` đã có sẵn. Màu chỉ có trong theme
   import (`sidebar-light/dark`) đổi sang `container-light/dark`. CSS biến `--primary` trong
   sheet-selection đổi sang `#007BFF`.
6. Icon `material-icons-outlined` → `material-symbols-outlined` với tên tương ứng.
7. CSS riêng của trang còn cần (vd. `.custom-radio`, `.sticky-actions`, `.custom-scrollbar`,
   `.no-scrollbar`) giữ trong một `<style>` đặt trong `@section('content')`.
8. `<script>` riêng của trang giữ trong `@section('content')` (dashboard không có `@stack`;
   `admin/reports/index` cũng đặt script trong content).
9. Việt hóa mọi chữ tiếng Anh hiển thị (tiêu đề, nhãn, nút, placeholder, thông báo rỗng).
10. Nút cố định (FAB "thêm khách", thanh `.sticky-actions` lưu hóa đơn) chỉnh vị trí đáy khi
    không còn bottom nav (vd. FAB `bottom-24` → `bottom-6` + safe-area).
11. **Loading (quyết định của Claude):** `layouts.dashboard` có `x-data="{ isLoading }"` và
    `<x-loading x-show="isLoading">`. Trong `x-data` của trang, **bỏ** `isLoading`/`loading` và
    handler `@click` bắt link (nếu giữ, biến lồng sẽ che biến của layout → không overlay nào hiện).
    Các form submit (upload Excel, "Tiếp tục" chọn sheet, lưu import, modal) đổi thành
    `@submit="isLoading = true"` — scope lồng của Alpine ghi thẳng vào biến của layout. Bỏ overlay
    riêng của import (mất câu "Đang xử lý dữ liệu…"; chấp nhận vì dùng overlay chung).
12. **In hóa đơn (quyết định của Claude):** thanh hành động đáy của `invoices/mobile_show`
    (In / PDF) **giữ lại** — nó là thanh hành động, không phải footer trang. Để `window.print()`
    chỉ in nội dung hóa đơn, thêm `print:hidden` vào sidebar, overlay, topbar, footer của
    `layouts.dashboard` và vào chính thanh hành động. Chỉ ảnh hưởng khi in; màn hình không đổi.
13. **Nút đổi sáng/tối (quyết định của Claude):** nút nổi trong `import-invoice-mobile` chỉ bật
    class `dark` cho riêng trang đó, không lưu lại, và không màn admin nào khác có → **bỏ**, để
    khung giống hệt các màn khác.

Sau khi cả 9 view chuyển xong:

- Xóa `resources/css/{mobile,mobile-compact,import}.css` khỏi `vite.config.js` **chỉ khi** không còn
  view nào dùng (`auth/login` đang dùng `mobile-compact`, `components/customer/app` dùng `mobile`)
  — giữ entry nào còn người dùng, xóa config/entry không còn ai dùng.
- Xóa `components/mobile-sidebar.blade.php` nếu không còn view dùng.
- Cập nhật test: `MobileSidebarTest` (3 test) đổi thành kiểm tra các trang này render sidebar của
  dashboard; `CustomerMobileListTest` giữ nguyên ý.

## Kiểm tra (định nghĩa xong)

- Feature test cho mỗi trang (UA mobile): status 200, có dấu hiệu layout dashboard (vd. `<x-topbar>`
  render và store `layout`), không còn chuỗi "CMS Inc." và các chuỗi tiếng Anh đã biết.
- Toàn bộ test hiện có pass; `npm run build` sạch.
- Kiểm tra trên trình duyệt ở 375px cho từng trang: không cuộn ngang, không thiếu class CSS, mở được
  sidebar, modal/form/nút back hoạt động. Hai bước import sau upload kiểm bằng render view
  (như đã làm trước) hoặc upload file mẫu nếu có.
- Desktop của các trang này không đổi (controller vẫn trả view desktop khi không phải mobile).

## Để người làm tự quyết

- Tên icon Material Symbols tương ứng; cách gom các khối `<style>` nhỏ.
- Bố cục dòng "Quay lại + tiêu đề" miễn giống nhau giữa các trang.
- Thứ tự làm các trang; có thể tách commit theo nhóm (khách hàng/dự án, hóa đơn, import).
