@extends('layouts.dashboard')

@section('title', 'Sửa hóa đơn '.($invoice->code ?? '#'.$invoice->id))
@section('page_heading', 'Sửa hóa đơn')

@section('content')
<div class="flex flex-col gap-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">Cập nhật hóa đơn</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hóa đơn: <span class="font-bold text-primary">{{ $invoice->code ?? '#' . $invoice->id }}</span></p>
            <p class="text-xs text-gray-400">Dự án: {{ $invoice->project->name }}</p>
        </div>
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="shrink-0"
              @submit="if (! confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')) { $event.preventDefault(); return; } isLoading = true">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex min-h-[44px] items-center gap-1 rounded-lg px-2 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                <span class="material-symbols-outlined text-xl">delete</span>
                {{ __('common.delete') }}
            </button>
        </form>
    </div>

    @include('invoices.partials.mobile-form', [
        'formId' => 'editInvoiceForm',
        'action' => route('invoices.update', $invoice->id),
        'method' => 'PUT',
        'cancelUrl' => route('projects.invoices.index', $invoice->project_id),
        'submitLabel' => 'Lưu thay đổi',
        'invoice' => $invoice,
        'initialItems' => old('items', $editItems->all()),
    ])
</div>
@endsection
