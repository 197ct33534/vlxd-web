// Alpine bundle qua Vite (thay cho CDN jsdelivr).
// Các listener 'alpine:init' khai báo inline trong layout vẫn chạy được, vì
// script module luôn chạy sau khi HTML đã parse xong.
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
