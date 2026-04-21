@extends('layouts.dashboard')
    
@section('title', 'Import Hóa Đơn')

@section('content')
<div x-data="{ loading: false, fileName: '' }" class="max-w-xl mx-auto py-12 relative animate-in fade-in zoom-in duration-300">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col items-center mb-8">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-4xl">folder</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Import Hóa Đơn Bán Hàng</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-center text-sm">Tải lên tệp Excel của bạn để bắt đầu xử lý dữ liệu hóa đơn.</p>
            </div>

            {{-- Thông báo --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-r-xl text-sm flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl text-sm flex items-center gap-3">
                    <span class="material-symbols-outlined">error</span>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('invoice.import') }}" method="POST" enctype="multipart/form-data" @submit="loading = true" class="space-y-6">
                @csrf
                @if(isset($projectId))
                    <input type="hidden" name="project_id" value="{{ $projectId }}">
                @endif

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">Chọn file Excel (.xlsx, .xls)</label>
                    <div class="relative group">
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-10 transition-all group-hover:border-primary group-hover:bg-blue-50/30 dark:group-hover:bg-primary/5 flex flex-col items-center justify-center cursor-pointer"
                             :class="fileName ? 'border-primary bg-blue-50/20' : ''">
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   @change="fileName = $event.target.files[0].name" />
                            
                            <template x-if="!fileName">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-primary mb-3 transition-colors">cloud_upload</span>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">
                                        <span class="text-primary font-bold">Nhấn để tải lên</span> hoặc kéo thả file vào đây
                                    </p>
                                    <p class="text-xs text-slate-400 mt-2">Định dạng hỗ trợ: XLSX, XLS (Tối đa 10MB)</p>
                                </div>
                            </template>
                            
                            <template x-if="fileName">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-primary">description</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-white" x-text="fileName"></p>
                                    <button type="button" @click.prevent="fileName = ''; $el.closest('form').querySelector('input[type=file]').value = ''" 
                                            class="mt-2 text-xs text-red-500 hover:underline font-semibold">
                                        Thay đổi file
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-2 transition-all transform hover:scale-[1.01] active:scale-[0.98] shadow-lg shadow-blue-200 dark:shadow-none">
                    <span class="material-symbols-outlined text-xl">upload_file</span>
                    Tiến hành Import
                </button>
            </form>
        </div>
        
        <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-sm">info</span>
                <span>Hệ thống hỗ trợ tệp Excel chuẩn từ các phần mềm kế toán phổ biến.</span>
            </div>
        </div>
    </div>
</div>

{{-- Popup loading overlay --}}
<div x-cloak x-show="loading"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center z-[100]"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl text-center max-w-sm w-full mx-4 transform transition-all animate-in zoom-in duration-300">
        <div class="flex justify-center mb-6">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Đang xử lý dữ liệu</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Vui lòng không đóng trình duyệt trong khi hệ thống đang xử lý tệp của bạn...</p>
    </div>
</div>
@endsection

