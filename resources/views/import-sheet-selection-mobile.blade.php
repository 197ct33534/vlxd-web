@extends('layouts.dashboard')

@section('title', 'Chọn sheet cần nhập')
@section('page_heading', 'Nhập hóa đơn Excel')

@section('content')
<div x-data="{ selectedIndex: 0 }" class="mx-auto flex w-full max-w-md flex-col gap-4">
    <div>
        <a href="{{ route('invoice.index', array_filter(['project_id' => $projectId ?? null])) }}" class="-ml-1 inline-flex min-h-[44px] items-center gap-1 text-sm font-semibold text-gray-500 hover:text-primary dark:text-gray-400">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            Chọn file khác
        </a>
        <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">Chọn sheet cần nhập</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">File có nhiều sheet. Chọn sheet chứa dữ liệu hóa đơn.</p>
    </div>

    <div class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <form action="{{ route('invoice.import') }}" method="POST" class="space-y-3" @submit="isLoading = true">
            @csrf
            <input type="hidden" name="stored_file_path" value="{{ $storedFilePath }}">
            @if(isset($projectId))
                <input type="hidden" name="project_id" value="{{ $projectId }}">
            @endif

            @foreach($sheetNames as $index => $name)
                <div>
                    <input type="radio" name="sheet_index" value="{{ $index }}" id="sheet_{{ $index }}"
                           class="peer hidden" x-model="selectedIndex" {{ $index === 0 ? 'checked' : '' }}>
                    <label for="sheet_{{ $index }}"
                           class="flex min-h-[52px] cursor-pointer items-center gap-3 rounded-lg border border-border-light bg-background-light p-3 transition-all active:scale-[0.98] peer-checked:border-primary peer-checked:bg-primary/5 dark:border-border-dark dark:bg-background-dark">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 dark:border-gray-600"
                              :class="selectedIndex == {{ $index }} ? 'border-primary' : ''">
                            <span class="h-2.5 w-2.5 rounded-full bg-primary transition-transform duration-200"
                                  :class="selectedIndex == {{ $index }} ? 'scale-100' : 'scale-0'"></span>
                        </span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200"
                              :class="selectedIndex == {{ $index }} ? 'text-primary' : ''">{{ $name }}</span>
                    </label>
                </div>
            @endforeach

            <button type="submit" class="mt-4 flex min-h-[48px] w-full items-center justify-center gap-2 rounded-lg bg-primary font-bold text-white shadow-lg shadow-primary/25 active:scale-[0.98]">
                Tiếp tục
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </button>
        </form>
    </div>
</div>
@endsection
