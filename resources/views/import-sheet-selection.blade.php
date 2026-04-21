@extends('layouts.dashboard')

@section('title', 'Select Sheet to Import')

@section('content')
<div class="max-w-md mx-auto py-12 relative animate-in fade-in zoom-in duration-300">
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8 relative">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-2xl mb-4">
                <span class="material-symbols-outlined text-3xl">folder</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Chọn Sheet để Import</h2>
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Tệp tải lên chứa nhiều trang tính. Vui lòng chọn trang bạn muốn nhập dữ liệu:
            </p>
        </div>

        <form action="{{ route('invoice.import') }}" method="POST" class="space-y-3" x-data="{ selectedSheet: 0 }">
            @csrf
            <input type="hidden" name="stored_file_path" value="{{ $storedFilePath }}">
            @if(isset($projectId))
                <input type="hidden" name="project_id" value="{{ $projectId }}">
            @endif

            <div class="space-y-3 max-h-60 overflow-y-auto pr-2 no-scrollbar">
                @foreach($sheetNames as $index => $name)
                    <div class="relative">
                        <input type="radio" name="sheet_index" value="{{ $index }}" id="sheet_{{ $index }}" 
                               class="hidden peer" x-model="selectedSheet"
                               {{ $index === 0 ? 'checked' : '' }}>
                        <label for="sheet_{{ $index }}" 
                               class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 transition-all peer-checked:border-primary peer-checked:bg-primary/5">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center peer-checked:border-primary">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary transition-transform duration-200"
                                     :class="selectedSheet == {{ $index }} ? 'scale-100' : 'scale-0'"></div>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" 
                    class="w-full mt-8 bg-primary hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center justify-center gap-2 group">
                <span>Tiếp tục Import</span>
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </button>
        </form>
    </div>
</div>
@endsection

