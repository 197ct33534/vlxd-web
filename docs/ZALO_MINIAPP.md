# Zalo Mini App — skeleton (Phase 3)

Plan: Mini App chỉ làm **skin** trên cùng portal PWA `/m` + API `/api/v1/*` khi đã có đơn thật từ PWA.

## Hiện có trong repo

| Path | Vai trò |
|------|---------|
| `public/zalo-miniapp/` | Entry HTML/JS + `app-config.json` |
| `/m` | Portal đặt hàng (auth SĐT) |
| `/api/v1/*` | API tài xế / admin (Sanctum) |

## Cách deploy thử

1. Publish Mini App trên Zalo với **start URL**:
   - `https://your-domain.com/zalo-miniapp/` (splash → redirect `/m`), hoặc
   - trực tiếp `https://your-domain.com/m`
2. Cấu hình OA / domain whitelist theo docs Zalo.
3. Không cần rewrite Laravel — session cookie + SĐT login giữ nguyên.

## Chưa làm (cố ý)

- Login bằng Zalo OAuth / map `zalo_user_id` ↔ `customers`
- Zalo JSBridge deep features (share, payment)
- Review store Mini App

Khi PWA đã có đơn ổn định: thêm bảng `customers.zalo_id` + soft-login từ Mini App context.
