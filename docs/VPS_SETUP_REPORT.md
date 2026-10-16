# Báo cáo dựng VLXD trên VPS

Ngày: 2026-10-16. Máy cài lại: Ubuntu 24.04.2 LTS, hostname `vlxd`, IP `221.121.4.174`.
1 vCPU, 954 MiB RAM, đĩa 20 GB (`/dev/sda2`, còn khoảng 14 GB sau khi dựng). Kernel `6.8.0-63-generic`.

Bản Ubuntu 20.04 trước đó đã bị xóa khi tạo lại máy ảo. Lần này dùng gói PHP có sẵn, không biên dịch mã nguồn.

Mã nguồn: `https://github.com/197ct33534/vlxd-web` tại `/var/www/vlxd`, commit `0ae201f`.

Không ghi mật khẩu SSH hay mật khẩu database. Mật khẩu database nằm ở `/root/.vlxd-db-password` (quyền `600`) và `DB_PASSWORD` trong `/var/www/vlxd/.env`.

## Kết quả

| Hạng mục | Trạng thái |
|----------|------------|
| Nginx 1.24.0-2ubuntu7.18 | active, root `/var/www/vlxd/public` |
| PHP 8.3.6 FPM (`8.3.6-0ubuntu0.24.04.11`) | active, socket `/run/php/php8.3-fpm.sock`, `pm=ondemand`, tối đa 2 process, `memory_limit=256M` |
| MariaDB 10.11.14 | active, database `vlxd`, user `vlxd`@`localhost` |
| Composer 2.10.3 | `composer install --no-dev --optimize-autoloader` |
| Git 2.43.0, unzip 6.0 | đã cài |
| Migrate + seed | xong |
| Kiểm tra trên VPS | `/` và `/login` HTTP 200; admin và portal demo đăng nhập được (302) |
| Truy cập từ Internet | TCP 80 vẫn timeout. UFW không được cài. Tường lửa nhà cung cấp cần mở cổng 80 |

URL khi cổng 80 đã mở: `http://221.121.4.174`

Tài khoản demo:

- Admin: `admin@vlxd.test` / `password` → `/login`
- Tài xế: `driver@vlxd.test` / `password` → `/driver`
- Portal: SĐT `0901234567` → `/m`

Trên 1 GB RAM, import Excel và xuất PDF vẫn có thể hết bộ nhớ. Đã thêm 1 GB swap và giảm MariaDB / PHP-FPM.

## Sửa sau khi clone

`app/Http/Kernel.php` trên commit `0ae201f` gọi class không tồn tại `Illuminate\Http\Middleware\ConvertEmptyStringsToNull`. Đã đổi thành `Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull`. Bản vá nằm trên server và trong working tree máy dev, chưa commit. Clone lại sẽ mất bản vá nếu chưa đưa vào git.

`umask 077` lúc tạo mật khẩu database làm `git clone` ra file `600` / thư mục `700`, Nginx báo `Permission denied`. Đã `chmod -R a+rX /var/www/vlxd`, giữ `.env` ở `640` `root:www-data`, và để `storage` cùng `bootstrap/cache` ghi được bởi `www-data`.

## Lệnh đã chạy

### 1. Swap 1 GB

```bash
fallocate -l 1G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
grep -q '/swapfile ' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
```

### 2. Gói hệ thống

```bash
export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=a
apt-get update
apt-get install -y nginx mariadb-server git unzip curl \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl
systemctl enable --now mariadb nginx php8.3-fpm
```

### 3. MariaDB gọn RAM và database

File `/etc/mysql/mariadb.conf.d/99-vlxd-lowmem.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 64M
performance_schema = OFF
max_connections = 30
table_open_cache = 64
tmp_table_size = 16M
max_heap_table_size = 16M
```

```bash
systemctl restart mariadb
umask 077
openssl rand -base64 24 | tr -d '\n' > /root/.vlxd-db-password
chmod 600 /root/.vlxd-db-password
# CREATE DATABASE vlxd utf8mb4; user vlxd@localhost; GRANT ALL ON vlxd.*
umask 022
```

### 4. PHP-FPM

Sửa `/etc/php/8.3/fpm/php.ini` và `/etc/php/8.3/cli/php.ini`: `memory_limit=256M`, `upload_max_filesize=16M`, `post_max_size=16M`, `date.timezone=Asia/Ho_Chi_Minh`.

Sửa `/etc/php/8.3/fpm/pool.d/www.conf`: `pm=ondemand`, `pm.max_children=2`, `pm.process_idle_timeout=10s`, `pm.max_requests=200`.

```bash
systemctl restart php8.3-fpm
```

### 5. Composer, clone, migrate

```bash
curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
git clone --depth 1 https://github.com/197ct33534/vlxd-web.git /var/www/vlxd
cd /var/www/vlxd
sed -i 's|\\Illuminate\\Http\\Middleware\\ConvertEmptyStringsToNull|\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull|' app/Http/Kernel.php
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=512M
composer install --no-dev --optimize-autoloader --no-interaction
cp .env.example .env
# APP_ENV=production APP_DEBUG=false APP_URL=http://221.121.4.174
# DB_DATABASE=vlxd DB_USERNAME=vlxd DB_PASSWORD từ /root/.vlxd-db-password
chmod 640 .env
chown root:www-data .env
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
chmod -R a+rX /var/www/vlxd
chmod 640 .env
```

Không chạy `npm run build`. Dashboard và portal dùng Tailwind CDN.

### 6. Nginx

File `/etc/nginx/sites-available/vlxd`, bật site, xóa site mặc định:

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name 221.121.4.174;
    root /var/www/vlxd/public;
    index index.php index.html;
    client_max_body_size 16M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
ln -sfn /etc/nginx/sites-available/vlxd /etc/nginx/sites-enabled/vlxd
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

### 7. Kiểm tra trên VPS

```bash
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1/
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1/login
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1/m
```

Kết quả sau khi sửa quyền file: `/` = 200, `/login` = 200, `/m` = 302 sang `/m/login`. Đăng nhập admin demo và SĐT portal đều 302 đúng trang.

Từ máy ngoài, `curl http://221.121.4.174/login` timeout. Cổng 80 chưa ra Internet.

## Việc còn lại phía nhà cung cấp

Mở inbound TCP **80** (và **443** khi gắn HTTPS) cho `221.121.4.174` trên panel firewall của VPS.
