# AGENTS.md — Bản đồ dự án vlxd-web

Laravel 10, Blade, Tailwind qua Vite, Alpine.js và MySQL. Ứng dụng quản lý cửa hàng vật liệu xây dựng. Giao diện nhân viên dùng session, portal khách dùng session riêng, API dùng Sanctum.

- [README.md](README.md) hướng dẫn chạy local và kiểm thử.
- [SYSTEM_CAPABILITIES.md](SYSTEM_CAPABILITIES.md) tra cứu module, vai trò và tên route.
- [routes/web.php](routes/web.php), [routes/api.php](routes/api.php) và [database/migrations](database/migrations) xác định chức năng đang chạy, middleware và schema.
- Kiểm tra `git status` trước khi sửa. Một số tệp hoặc migration trên máy có thể chưa được Git theo dõi. Khi mô tả tính năng đã triển khai, đối chiếu với route và các tệp đã commit.

## Điểm vào theo nghiệp vụ

- Khách hàng và công trình ở `CustomerController`, `ProjectController`, `resources/views/customer/` và `resources/views/projects/`. Khách hàng có nhiều công trình. Mỗi hóa đơn thuộc một công trình.
- Hóa đơn, PDF và phiếu thu ở `InvoiceController` và `resources/views/invoices/`. Trang danh sách dùng `projects.invoices.index`. Trang chi tiết và modal Xem nhanh dùng chung `invoices/partials/details.blade.php` trên desktop.
- Nhập Excel ở `ImportInvoiceController`, `InvoiceImportMatcher` và `resources/views/import-workbook.blade.php`. PhpSpreadsheet đọc workbook. `invoice.workbook` và `invoice.workbook.save` xử lý xem trước và lưu nhiều sheet. `invoice.save` là luồng cũ, `saveSimple` không gắn route.
- Báo cáo ngày, báo cáo quản trị, nhân viên và bảng giá có route riêng. Tra cứu trong bản đồ tính năng.
- Portal khách `/m`, quản lý đơn `/admin/orders` và tài xế `/driver` cùng dùng `Order` và `OrderItem`. Theo dõi tác động trên cả ba giao diện khi đổi trạng thái hoặc schema đơn hàng.

## Quy tắc dữ liệu cần giữ

- `invoices.invoice_date` là ngày xuất. `invoice_items.date` là ngày giao của từng dòng. `payments.payment_date` là ngày thu. Gửi `Y-m-d` qua form và hiển thị `d/m/Y`. Lịch tiếng Việt dùng directive `x-vietnamese-date` trong `resources/js/alpine.js`.
- Ngày xuất Excel lấy từ dòng ngày ký/lập hóa đơn khi nhận diện được. Nếu thiếu hoặc có nhiều ngày khớp, yêu cầu nhập ngày xuất. Ngày giao lấy từ cột ngày trên từng dòng. Nếu file Excel có dấu hiệu đảo ngày và tháng, đối chiếu nguồn trước khi sửa hàng loạt.
- Bản xem trước workbook nằm trong cache theo UUID và người tải, hết hạn sau hai giờ. Khi lưu, kiểm tra lại công trình đích và trạng thái trùng ở server. Mỗi sheet lưu trong giao dịch riêng. Mã nhận diện nội dung có ràng buộc duy nhất theo công trình. Hóa đơn cũ khớp vật liệu và tổng tiền chỉ là kết quả nghi trùng.
- Phiếu thu thuộc công trình qua `payments.project_id`, còn số tiền đã thu theo từng hóa đơn lấy từ `payment_allocations`. Dùng `PaymentAllocationService` cho phân bổ chung và `ProjectDebtService::recalculate()` sau khi đổi hóa đơn hoặc phiếu thu.
- Thao tác thu đủ chỉ ghi phần còn thiếu vào hóa đơn đã chọn. Khi chuyển hóa đơn, công trình đích phải thuộc cùng khách hàng. Luồng hiện tại chặn hóa đơn có phân bổ tiền hoặc đơn hàng liên kết và tính lại công nợ của cả hai công trình sau khi chuyển.
- Schema phiếu thu dùng `mot_lan` và `tung_dot`. Dùng `Payment::normalizeType()` để chuẩn hóa giá trị nhập. Kiểm tra tên cột trong migration trước khi dùng các cột chỉ có ở một số bộ dữ liệu local.

## Giao diện và kiểm thử

- Giữ khung `resources/views/layouts/dashboard.blade.php`, sidebar và breadcrumb dùng chung. Kiểm tra cả view desktop và mobile, nhất là view riêng có tiền tố `mobile_`. Modal xem nhanh và PDF có thể cần kiểm tra riêng.
- Chạy `npm run build` khi thay Blade, CSS hoặc JS liên quan đến Vite. Chạy `vendor/bin/pest` và ưu tiên các bài kiểm thử theo module.
- Kiểm thử tự động dùng SQLite trong bộ nhớ. Nếu cần đối chiếu dữ liệu local, xác định đúng database trước khi sửa. Giữ `.env`, cơ sở dữ liệu, bản sao lưu và Excel nghiệp vụ ngoài commit.
