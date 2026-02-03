@extends('layouts.app')

@section('title', 'Upload Images')

@section('content')
<div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden bg-background-light dark:bg-background-dark font-display text-[#0d121b] dark:text-white" x-data="imageUploader()">
    
    <!-- TopNavBar (Simulated) -->
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-10 py-3 shrink-0">
        <div class="flex items-center gap-8">
            <h2 class="text-lg font-bold">Image Manager / Upload</h2>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('images.index') }}" class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 h-10 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                <span>Back to Gallery</span>
            </a>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto px-4 lg:px-12 py-8">
        <div class="max-w-[1200px] mx-auto">
            <!-- PageHeading -->
            <div class="flex flex-wrap justify-between gap-3 mb-8">
                <div class="flex min-w-72 flex-col gap-1">
                    <p class="text-[#0d121b] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">Upload Images</p>
                    <p class="text-[#4c669a] text-base font-normal leading-normal">Add new assets to your library via direct upload or remote URL.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Left: Drag & Drop Zone -->
                <div class="flex flex-col bg-white dark:bg-slate-900 rounded-xl shadow-sm p-6">
                    <h3 class="text-[#0d121b] dark:text-white text-lg font-bold mb-4">Direct Upload</h3>
                    
                    <div 
                        class="flex flex-col items-center justify-center gap-6 rounded-xl border-2 border-dashed px-6 py-20 transition-all cursor-pointer group relative"
                        :class="isDragging ? 'border-primary bg-primary/10' : 'border-primary/30 bg-primary/5 hover:border-primary'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                    >
                        <input type="file" x-ref="fileInput" class="hidden" multiple accept="image/*" @change="handleFiles($event.target.files)">

                        <div class="size-16 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-3xl">upload_file</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <p class="text-[#0d121b] dark:text-white text-xl font-bold leading-tight tracking-[-0.015em] text-center">Drag &amp; drop images here</p>
                            <p class="text-primary text-base font-semibold text-center">or click to browse files</p>
                        </div>
                        <div class="text-slate-500 text-sm font-normal leading-normal text-center bg-white/50 dark:bg-slate-800/50 px-4 py-2 rounded-full">
                            Supports JPG, PNG, GIF, WEBP up to 5MB.
                        </div>
                        <button class="flex min-w-[140px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-6 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">
                            <span class="truncate">Select Files</span>
                        </button>

                         <!-- Loading Overlay -->
                        <div x-show="uploading" class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 flex items-center justify-center rounded-xl z-10 backdrop-blur-sm" style="display: none;">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-symbols-outlined animate-spin text-primary text-4xl">progress_activity</span>
                                <p class="text-primary font-bold">Uploading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: URL & Preview Side -->
                <div class="flex flex-col gap-6">
                    <!-- Upload by URL Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm overflow-hidden flex flex-col h-full border border-slate-100 dark:border-slate-800">
                        <div class="w-full bg-center bg-no-repeat aspect-[21/9] bg-cover relative" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAbGczqvwyBPFQpdFIuHRyVj6mmX73VN_sc8S7CiMyVZQYRag9DxitpVlhOn13QOuGSwfZSq_AA-UY5NIPnVn_fN13ov4gAEseJcYZZrbmd42mQWf8Wokl7zBqlDy9OJ26BvsucbyAA_g7gRgm0kazd2JUixTQ5R6xbyDFgkTaa_6VR_1og2rq8Dcnqo4vsH2kM6EQXQLGJ4GgQpCp2Y_paVdg0cb84GMATih9BKr6Lo9CXQhPzxu79BHiM8554vEpeQfhfqgY7r1Y");'>
                            <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-slate-900 to-transparent"></div>
                        </div>
                        <div class="flex flex-col p-6 gap-4">
                            <div>
                                <h3 class="text-[#0d121b] dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">Upload by URL</h3>
                                <p class="text-[#4c669a] text-sm font-normal mt-1 leading-normal">Import images from external websites by providing a direct link.</p>
                            </div>
                            <div class="flex flex-col gap-4 mt-2">
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Image URL</label>
                                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-lg p-1 border border-transparent focus-within:border-primary/50 transition-all">
                                        <div class="pl-3 text-slate-400">
                                            <span class="material-symbols-outlined text-sm">link</span>
                                        </div>
                                        <input x-model="urlInput" @keyup.enter="handleUrlUpload()" class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 placeholder:text-slate-400 dark:text-white" placeholder="https://example.com/image.jpg" type="text"/>
                                    </div>
                                </div>
                                <button @click="handleUrlUpload()" :disabled="urlUploading || !urlInput" class="w-full flex items-center justify-center gap-2 rounded-lg h-12 bg-primary text-white text-sm font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="!urlUploading">
                                        <span class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-base">cloud_download</span>
                                            <span>Download &amp; Save Image</span>
                                        </span>
                                    </template>
                                    <template x-if="urlUploading">
                                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tips/Helper Card -->
                    <div class="bg-primary/5 border border-primary/10 rounded-xl p-6 flex items-start gap-4">
                        <div class="size-10 rounded-lg bg-primary/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary">info</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[#0d121b] dark:text-white font-bold text-sm">Pro Tip: Bulk Upload</p>
                            <p class="text-slate-600 dark:text-slate-400 text-xs">You can select multiple files at once from your file explorer to upload them in a batch. Each file will be processed individually.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Result List (Optional: Show recently uploaded files here) -->
        </div>
    </div>
    
     <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-8 right-8 z-[60] flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl rounded-xl px-5 py-4"
         style="display: none;">
        <div class="flex items-center justify-center rounded-full size-8"
             :class="toastType === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'">
            <span class="material-symbols-outlined text-[20px]" x-text="toastType === 'success' ? 'check' : 'error'"></span>
        </div>
        <div class="flex flex-col">
            <p class="text-sm font-bold text-slate-800 dark:text-white" x-text="toastMessage || 'Notification'"></p>
            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="toastSubMessage || ''"></p>
        </div>
        <button @click="showToast = false" class="ml-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

</div>

<script>
    function imageUploader() {
        return {
            isDragging: false,
            uploading: false,
            urlInput: '',
            urlUploading: false,
            showToast: false,
            toastMessage: '',
            toastSubMessage: '',
            toastType: 'success',

            handleDrop(event) {
                this.isDragging = false;
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.handleFiles(files);
                }
            },

            handleFiles(files) {
                if (files.length === 0) return;
                
                this.uploading = true;
                const promises = [];

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.startsWith('image/')) {
                         this.notify('Error', `${file.name} is not an image.`, 'error');
                         continue;
                    }

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('alt_text', file.name); // Default alt text

                    const uploadPromise = fetch('/api/upload/image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            return { success: true, name: file.name };
                        } else {
                            throw new Error(data.message || 'Upload failed');
                        }
                    });

                    promises.push(uploadPromise);
                }

                Promise.allSettled(promises).then((results) => {
                    const successCount = results.filter(r => r.status === 'fulfilled').length;
                    const failCount = results.filter(r => r.status === 'rejected').length;

                    if (successCount > 0) {
                        this.notify('Success', `Uploaded ${successCount} image(s) successfully.`);
                    }
                    if (failCount > 0) {
                        // Optional: notify about failures
                        console.error('Some uploads failed');
                    }
                }).finally(() => {
                    this.uploading = false;
                    // Reset input
                    this.$refs.fileInput.value = '';
                });
            },

            handleUrlUpload() {
                if (!this.urlInput) return;
                
                this.urlUploading = true;

                fetch('/api/upload/image-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        url: this.urlInput,
                        alt_text: 'Imported from URL'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.notify('Success', 'Image downloaded and saved successfully.');
                        this.urlInput = '';
                    } else {
                        this.notify('Error', data.message || 'Failed to download image', 'error');
                    }
                })
                .catch(err => {
                    this.notify('Error', 'System error occurred.', 'error');
                    console.error(err);
                })
                .finally(() => {
                    this.urlUploading = false;
                });
            },

            notify(message, subMessage, type = 'success') {
                this.toastMessage = message;
                this.toastSubMessage = subMessage;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            }
        }
    }
</script>
@endsection
