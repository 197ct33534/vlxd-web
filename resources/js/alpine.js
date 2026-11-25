// Alpine bundle qua Vite (thay cho CDN jsdelivr).
// Các listener 'alpine:init' khai báo inline trong layout vẫn chạy được, vì
// script module luôn chạy sau khi HTML đã parse xong.
import Alpine from 'alpinejs';

Alpine.directive('vietnamese-date', (element, { expression }, { cleanup, effect, evaluateLater }) => {
    let picker;
    let disposed = false;
    const id = element.id;

    cleanup(() => {
        disposed = true;
        picker?.destroy();
        element.id = id;
    });

    import('./vietnamese-date-picker').then(({ createVietnameseDatePicker }) => {
        if (disposed || !element.isConnected) return;

        picker = createVietnameseDatePicker(element);
        if (expression) {
            const readDate = evaluateLater(expression);
            effect(() => readDate((date) => {
                const selected = picker.selectedDates[0];
                const current = selected ? picker.formatDate(selected, 'Y-m-d') : '';
                if ((date ?? '') !== current) picker.setDate(date || [], false);
            }));
        }
    });
});

window.Alpine = Alpine;
Alpine.start();
