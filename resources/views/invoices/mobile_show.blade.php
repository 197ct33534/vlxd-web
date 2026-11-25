@extends('layouts.dashboard')

@section('title', 'Hóa đơn '.($invoice->code ?? '#'.$invoice->id))
@section('page_heading', 'Chi tiết hóa đơn')

@section('content')
<div class="flex flex-col gap-4 pb-28 print:pb-0">
    <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" class="-ml-1 inline-flex min-h-[44px] items-center gap-1 self-start text-sm font-semibold text-gray-500 hover:text-primary dark:text-gray-400 print:hidden">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
        Quay lại danh sách hóa đơn
    </a>

    {{-- Mã + trạng thái --}}
    <section class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Mã hóa đơn</p>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ $invoice->code ?? '#' . $invoice->id }}</h1>
            </div>
            <span class="rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                Đã thanh toán
            </span>
        </div>
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Ngày xuất</dt>
                <dd class="text-sm font-medium">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</dd>
            </div>
            <div class="text-right">
                <dt class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Dự án</dt>
                <dd class="truncate text-sm font-medium">{{ $invoice->project->name }}</dd>
            </div>
        </dl>
    </section>

    {{-- Khách hàng + công trình --}}
    <section class="grid gap-3">
        <div class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
            <div class="mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl text-primary">person</span>
                <h2 class="font-bold text-gray-700 dark:text-gray-300">Khách hàng</h2>
            </div>
            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $invoice->project->customer->name ?? '—' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->project->customer->phone ?? '' }}</p>
            <p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $invoice->project->customer->address ?? '' }}</p>
        </div>

        <div class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
            <div class="mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl text-primary">assignment</span>
                <h2 class="font-bold text-gray-700 dark:text-gray-300">Chi tiết dự án</h2>
            </div>
            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $invoice->project->name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->project->address }}</p>
        </div>
    </section>

    {{-- Dòng vật liệu --}}
    <section>
        <div class="mb-3 flex items-center justify-between px-1">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Sản phẩm / Dịch vụ</h2>
            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-800">{{ $invoice->items->count() }} mục</span>
        </div>
        <div class="space-y-3">
            @foreach($invoice->items as $item)
                <div class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                    <div class="mb-2 flex items-start justify-between gap-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                        <span class="shrink-0 font-bold text-gray-900 dark:text-white">{{ number_format($item->amount) }}đ</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>Số lượng: {{ $item->quantity }} {{ $item->unit }}</span>
                        <span>{{ number_format($item->price) }}đ / đơn vị</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Tổng tiền --}}
    <section class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <div class="flex justify-between gap-3 text-sm">
            <span class="text-gray-500 dark:text-gray-400">Ghi chú</span>
            <span class="text-right font-medium italic">{{ $invoice->note ?? '-' }}</span>
        </div>
        @if($invoice->total_text)
            <p class="mt-2 text-right text-xs italic text-gray-400">({{ $invoice->total_text }})</p>
        @endif
        <div class="mt-3 flex items-center justify-between border-t border-border-light pt-3 dark:border-border-dark">
            <span class="text-lg font-bold text-gray-900 dark:text-white">Tổng cộng</span>
            <span class="text-2xl font-black text-primary">{{ number_format($invoice->total_amount) }}đ</span>
        </div>
    </section>

    <p class="text-center text-xs text-gray-400">Hóa đơn được tạo tự động bởi hệ thống.</p>

    {{-- Thanh thao tác dính đáy: In / PDF (ẩn khi in). --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-border-light bg-container-light/95 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))] backdrop-blur-md dark:border-border-dark dark:bg-container-dark/95 lg:left-64 print:hidden">
        <div class="mx-auto grid max-w-md grid-cols-2 gap-3">
            <button type="button" onclick="window.print()" class="flex min-h-[48px] items-center justify-center gap-2 rounded-lg bg-gray-100 font-bold text-gray-900 active:scale-[0.98] dark:bg-gray-800 dark:text-gray-100">
                <span class="material-symbols-outlined text-[20px]">print</span>
                {{ __('invoices.print') }}
            </button>
            <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="flex min-h-[48px] items-center justify-center gap-2 rounded-lg bg-primary font-bold text-white shadow-lg shadow-primary/25 active:scale-[0.98]">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                {{ __('invoices.pdf') }}
            </a>
        </div>
    </div>
</div>
@endsection
