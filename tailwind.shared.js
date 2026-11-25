// Phần dùng chung cho mọi bản build Tailwind (admin + portal).
// Mỗi theme có file riêng vì font và bo góc khác nhau — gộp lại sẽ làm lệch giao diện.
export const content = [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './public/js/**/*.js',
    // Badge/màu trạng thái có thể được trả về từ model/controller.
    './app/**/*.php',
];
