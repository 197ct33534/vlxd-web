@if ($summary = session('import_summary'))
    <p role="status" class="rounded-lg border {{ $summary['failed'] ? 'border-amber-300 bg-amber-50 text-amber-900 dark:bg-amber-950 dark:text-amber-200' : 'border-emerald-300 bg-emerald-50 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-200' }} p-4 text-sm font-semibold">
        @if ($summary['saved'])
            Đã lưu {{ $summary['saved'] }} sheet thành hóa đơn.
        @endif
        @if ($summary['duplicate'])
            Đã bỏ qua {{ $summary['duplicate'] }} sheet đã nhập.
        @endif
        @if ($summary['failed'])
            Có {{ $summary['failed'] }} sheet chưa lưu được. Xem lỗi bên dưới để thử lại.
        @endif
    </p>
@endif
