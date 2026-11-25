import flatpickr from 'flatpickr';
import { Vietnamese } from 'flatpickr/dist/l10n/vn.js';
import 'flatpickr/dist/flatpickr.css';

export function createVietnameseDatePicker(element) {
    return flatpickr(element, {
        locale: { ...Vietnamese, yearAriaLabel: 'Năm', monthAriaLabel: 'Tháng' },
        disableMobile: true,
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        ariaDateFormat: 'l, d F Y',
        allowInput: true,
        parseDate(value, format) {
            const date = flatpickr.parseDate(value, format);
            return date && flatpickr.formatDate(date, format) === value ? date : undefined;
        },
        onReady(_dates, _value, picker) {
            const display = picker.altInput;
            display.placeholder = 'dd/mm/yyyy';
            const label = element.getAttribute('aria-label');
            if (label) display.setAttribute('aria-label', label);
            if (element.id) {
                display.id = element.id;
                element.removeAttribute('id');
            }
        },
    });
}
