# Mobile-First VLXD — hướng dẫn vận hành

## Chạy migration

```bash
composer install --ignore-platform-reqs   # nếu PHP < 8.3 (zipstream)
# Local nhanh: DB_CONNECTION=sqlite + file database/database.sqlite
# Production: cấu hình MySQL trong .env
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

**Tài khoản demo** (`MobileDemoSeeder`):
| Vai trò | Đăng nhập |
|---------|-----------|
| Admin shop | `admin@vlxd.test` / `password` → `/admin/orders` |
| Tài xế | `driver@vlxd.test` / `password` → `/driver` |
| Portal khách | SĐT `0901234567` → `/m` |

Cập nhật `store_info.phone` / `zalo_oa_url` trong DB để nút Gọi / Zalo hiện đúng.

## URL chính

| Ai | URL | Ghi chú |
|----|-----|---------|
| Khách (thầu/thợ) | `/m` | Đăng nhập bằng SĐT khớp `customers.phone` |
| Shop admin | `/admin/orders` | Sửa giá, xác nhận, gán tài xế, **xuất hóa đơn** |
| Tài xế | `/driver` | User `role=driver` (hoặc admin) |
| API | `/api/v1/*` | Token: `POST /api/v1/auth/token` |
| Tính KL | `/m/calculator` | Bê tông / gạch / vữa → đẩy sang đặt hàng |
| Cửa hàng | `/admin/store-info` | SĐT + Zalo CTA portal |

## API tài xế (Sanctum)

```http
POST /api/v1/auth/token
{ "email": "driver@vlxd.test", "password": "password", "device_name": "android" }

GET  /api/v1/orders
GET  /api/v1/orders/{id}
POST /api/v1/orders/{id}/start
POST /api/v1/orders/{id}/complete   # multipart: delivery_photo, delivery_lat, delivery_lng
DELETE /api/v1/auth/token
```

Header: `Authorization: Bearer {token}`

Telegram nội bộ (optional): `TELEGRAM_BOT_TOKEN` + `TELEGRAM_CHAT_ID` — nhận tin khi khách đặt đơn mới.

## PWA

- Manifest: `/manifest.webmanifest`
- Service worker: `/sw.js` (cache shell `/m`)
- Trên Android Chrome: mở `/m` → Add to Home Screen

## Zalo ZNS

Điền biến trong `.env` (xem `.env.example`). Không có token thì `ZaloNotifier` chỉ ghi log — an toàn cho local.

## Mini App

Skeleton: `/zalo-miniapp/` → redirect `/m`. Chi tiết: [ZALO_MINIAPP.md](./ZALO_MINIAPP.md).

OAuth Zalo ↔ customer: làm sau khi PWA có traffic đơn thật.

## Portal poll trạng thái

`GET /m/orders/{order}/status` — JSON; trang chi tiết đơn tự poll mỗi 8s đến khi `delivered`/`cancelled`.
