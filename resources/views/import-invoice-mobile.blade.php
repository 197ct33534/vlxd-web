@extends('layouts.dashboard')

@section('title', 'Nhập hóa đơn Excel')
@section('page_heading', 'Nhập hóa đơn Excel')

@section('content')
<div x-data="{ fileName: '' }" class="mx-auto flex w-full max-w-md flex-col gap-4">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">Nhập hóa đơn bán hàng</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Đọc dữ liệu hóa đơn từ tệp Excel.</p>
    </div>

    @if(session('error') || $errors->any())
        <p role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ session('error') ?? $errors->first() }}</p>
    @endif

    <div class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <form action="{{ route('invoice.import') }}" method="POST" enctype="multipart/form-data" @submit="isLoading = true" class="space-y-5 p-4">
            @csrf
            @if(isset($projectId))
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dự án</label>
                    <div class="rounded-lg border border-primary/20 bg-primary/5 p-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $importProject->name }}
                    </div>
                </div>
            @endif

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Chọn file Excel (.xlsx, .xls)</label>
                <label for="file-upload" class="flex min-h-[64px] cursor-pointer items-center gap-3 rounded-lg border-2 border-dashed border-gray-300 bg-background-light p-3 transition-colors hover:border-primary dark:border-gray-700 dark:bg-background-dark">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-container-light shadow-sm dark:bg-container-dark">
                        <span class="material-symbols-outlined text-2xl" :class="fileName ? 'text-primary' : 'text-gray-400'">description</span>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-gray-700 dark:text-gray-200" x-text="fileName ? fileName : 'Nhấn để chọn tệp tin'"></span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Excel tối đa 10MB</span>
                    </span>
                    <span class="material-symbols-outlined text-gray-400" :class="fileName ? 'text-primary' : ''">attach_file</span>
                    <input name="file" accept=".xlsx, .xls" class="hidden" id="file-upload" type="file" required @change="fileName = $event.target.files[0]?.name ?? ''"/>
                </label>
            </div>

            <button type="submit" class="flex min-h-[48px] w-full items-center justify-center gap-2 rounded-lg bg-primary font-bold text-white shadow-lg shadow-primary/25 active:scale-[0.98]">
                <span class="material-symbols-outlined text-xl">upload_file</span>
                Tiến hành nhập
            </button>
        </form>
        <div class="flex items-start gap-2 border-t border-border-light bg-background-light px-4 py-3 text-xs leading-relaxed text-gray-500 dark:border-border-dark dark:bg-background-dark dark:text-gray-400">
            <span class="material-symbols-outlined text-base">info</span>
            Hỗ trợ tệp Excel từ các phần mềm kế toán phổ biến.
        </div>
    </div>
</div>
@endsection
