@extends('layouts.dashboard')

@section('title', 'Tạo hóa đơn – '.$project->name)
@section('page_heading', 'Tạo hóa đơn')

@section('content')
<div class="flex flex-col gap-4">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">Hóa đơn mới</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dự án: <span class="font-bold text-primary">{{ $project->name }}</span></p>
    </div>

    @include('invoices.partials.mobile-form', [
        'formId' => 'invoiceForm',
        'action' => route('projects.invoices.store', $project->id),
        'method' => 'POST',
        'cancelUrl' => route('projects.invoices.index', $project->id),
        'submitLabel' => 'Lưu hóa đơn',
        'invoice' => null,
        'initialItems' => [],
    ])
</div>
@endsection
