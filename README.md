# vlxd-web

Ứng dụng quản lý cửa hàng vật liệu xây dựng, gồm khách hàng, công trình, hóa đơn, công nợ và giao hàng. Xem [AGENTS.md](AGENTS.md) để tìm điểm vào khi sửa code và [SYSTEM_CAPABILITIES.md](SYSTEM_CAPABILITIES.md) để tra module và route.

## Chạy với Laragon hoặc PHP local

Dùng PHP 8.3 hoặc 8.4, Composer và Node.js 22. PHP cần các extension theo Composer và tính năng đang dùng, gồm `intl` cho số tiền bằng chữ và `pdo_mysql` hoặc `pdo_sqlite` cho cơ sở dữ liệu.

1. Sao chép `.env.example` thành `.env` nếu chưa có. Đặt `APP_URL` theo địa chỉ local và cấu hình cơ sở dữ liệu.
2. Chạy `composer install` và `php artisan key:generate` nếu chưa có `APP_KEY`.
3. Chạy `npm ci`, `npm run build` và `php artisan migrate`.
4. Trong Laragon, cấu hình web root vào thư mục `public/` và bật Apache rewrite. Nếu dùng PHP CLI, chạy `php artisan serve`.

Nếu dùng SQLite local, tạo file `database/local.sqlite`, đặt `DB_CONNECTION=sqlite` và trỏ `DB_DATABASE` tới đường dẫn tuyệt đối của file. Với MySQL, điền host, port, tên database và tài khoản của máy đang chạy. Dữ liệu demo có thể tạo bằng `php artisan db:seed` khi dùng database dành riêng để thử. Xem `database/seeders/MobileDemoSeeder.php` để biết tài khoản demo.

## Chạy với Docker Compose

`docker compose up -d --build` khởi động dịch vụ `web` trên cổng `8000` và MySQL qua cổng host `3308`. Laravel chạy trong container dùng `DB_HOST=db` và `DB_PORT=3306`. Thiết lập tên database và tài khoản khớp cấu hình dịch vụ `db` trong `docker-compose.yml`.

Compose gắn thư mục dự án vào `/var/www/html`. Nếu thiếu dependency hoặc tài nguyên trong thư mục được gắn, chạy `docker compose exec web composer install`, rồi chạy `npm ci` và `npm run build` ở máy host. Tạo app key nếu thiếu và chạy migration qua `docker compose exec web php artisan migrate`.

## Kiểm tra thay đổi

- `npm run build` để xác nhận build CSS/JS.
- `php vendor/bin/pest` để chạy test. `phpunit.xml` dùng SQLite `:memory:`.
- `php artisan route:list` để tra route đang được khai báo.
