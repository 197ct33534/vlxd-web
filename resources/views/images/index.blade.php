@extends('layouts.app')

@section('title', 'Image Manager')

@section('content')
<!-- Font & Icons from Template -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .active-icon {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>

<div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100" x-data="imageManager()">

    <!-- TopNavBar (Simulated from Template inside Content area or replacing content header) -->
    <header class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-8 flex-1">
            <h2 class="text-lg font-bold">Image Manager</h2>
            <div class="max-w-md w-full">
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                    <input class="w-full h-10 pl-10 pr-4 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Search your assets..." type="text"/>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('images.create') }}" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 h-10 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">cloud_upload</span>
                <span>Upload</span>
            </a>
            <button @click="uploadUrlOpen = true" class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 h-10 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">link</span>
                <span>Upload by URL</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8" @scroll="checkScroll($event)">
        <!-- SectionHeader & Tabs -->
        <div class="mb-6">
            <div class="flex items-end justify-between border-b border-slate-200 dark:border-slate-800">
                <div class="flex gap-8">
                    <a class="pb-3 border-b-2 border-primary text-primary text-sm font-bold" href="#">All Images</a>
                    <a class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium" href="#">Recently Added</a>
                    <a class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium" href="#">Folders</a>
                    <a class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium" href="#">Favorites</a>
                </div>
                <div class="pb-3 flex items-center gap-2 text-slate-400">
                    <span class="material-symbols-outlined cursor-pointer hover:text-primary">grid_view</span>
                    <span class="material-symbols-outlined cursor-pointer hover:text-primary">list</span>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Recent Uploads</h3>
        </div>

        <!-- ImageGrid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            <template x-for="image in images" :key="image.id">
                <div class="group relative bg-white dark:bg-slate-900 p-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 hover:shadow-md transition-all">
                    <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 mb-3 relative">
                        <div class="w-full h-full bg-cover bg-center cursor-pointer" 
                             :style="`background-image: url('${image.url}')`"
                             @click="viewImage(image)">
                        </div>
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 pointer-events-none group-hover:pointer-events-auto">
                            <button @click.stop="copyUrl(image.url)" class="w-10 h-10 rounded-full bg-white text-slate-900 flex items-center justify-center hover:bg-primary hover:text-white transition-colors" title="Copy Link">
                                <span class="material-symbols-outlined text-[20px]">link</span>
                            </button>
                            <button @click.stop="activeImage = image; editImageOpen = true;" class="w-10 h-10 rounded-full bg-white text-slate-900 flex items-center justify-center hover:bg-primary hover:text-white transition-colors" title="Edit Image">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <button @click.stop="deleteImage(image.id)" class="w-10 h-10 rounded-full bg-white text-slate-900 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Delete">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1 px-1">
                        <p class="text-sm font-semibold truncate dark:text-slate-200" x-text="image.filename"></p>
                        <p class="text-[10px] text-slate-500 font-medium uppercase" x-text="`${formatDate(image.created_at)} • ${formatSize(image.size)}`"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <template x-if="images.length === 0 && !loading">
             <div class="text-center py-12 text-gray-500">
                Chưa có hình ảnh nào.
            </div>
        </template>

        <!-- Loading -->
        <template x-if="loading">
             <div class="text-center py-12 text-primary">
                Đang tải dữ liệu...
            </div>
        </template>
        
        <!-- Load More Trigger (can be replaced by infinite scroll logic in checkScroll) -->
        <div x-show="nextPageUrl && !loading" class="text-center mt-6">
             <button @click="loadMore()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded hover:bg-slate-200 text-sm font-bold">
                Load More
            </button>
        </div>

    </div>

    <!-- Modal Preview (Integrated from Template 2) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display: none;" x-transition.opacity>
        <!-- Modal Container -->
        <div class="bg-white dark:bg-[#1a2234] w-full max-w-[1000px] max-h-[90vh] overflow-hidden rounded-xl shadow-2xl flex flex-col md:flex-row relative" @click.away="modalOpen = false">
            <!-- Close Button (Mobile/Tablet visible) -->
            <button class="absolute top-4 right-4 text-white md:hidden" @click="modalOpen = false">
                <span class="material-symbols-outlined">close</span>
            </button>
            
            <!-- Left: Image Preview Section -->
            <div class="flex-1 bg-slate-100 dark:bg-slate-900 flex items-center justify-center p-6 min-h-[400px]">
                <div class="relative w-full h-full flex items-center justify-center">
                    <img :src="activeImage?.url" class="max-w-full max-h-[70vh] rounded-lg shadow-lg object-contain">
                    <div class="absolute top-4 right-4">
                        <button class="bg-white/80 dark:bg-black/40 hover:bg-white dark:hover:bg-black/60 p-2 rounded-full transition-colors backdrop-blur-md">
                            <span class="material-symbols-outlined text-slate-800 dark:text-white">zoom_in</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Details Sidebar -->
            <div class="w-full md:w-[380px] bg-white dark:bg-[#1a2234] border-l border-slate-200 dark:border-slate-800 flex flex-col h-full">
                <div class="p-6 flex-1 overflow-y-auto">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-[#0d121b] dark:text-white text-[22px] font-bold leading-tight tracking-[-0.015em]">Image Details</h2>
                        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors hidden md:block" @click="modalOpen = false">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Metadata List -->
                    <div class="space-y-0" x-show="activeImage">
                        <div class="grid grid-cols-2 border-t border-slate-200 dark:border-slate-800 py-4">
                            <p class="text-[#4c669a] dark:text-slate-400 text-sm font-normal">Filename</p>
                            <p class="text-[#0d121b] dark:text-white text-sm font-medium text-right truncate" x-text="activeImage?.filename"></p>
                        </div>
                        <div class="grid grid-cols-2 border-t border-slate-200 dark:border-slate-800 py-4">
                            <p class="text-[#4c669a] dark:text-slate-400 text-sm font-normal">MIME Type</p>
                            <p class="text-[#0d121b] dark:text-white text-sm font-medium text-right" x-text="activeImage?.mime_type"></p>
                        </div>
                        <div class="grid grid-cols-2 border-t border-slate-200 dark:border-slate-800 py-4">
                            <p class="text-[#4c669a] dark:text-slate-400 text-sm font-normal">File Size</p>
                            <p class="text-[#0d121b] dark:text-white text-sm font-medium text-right" x-text="formatSize(activeImage?.size)"></p>
                        </div>
                        <div class="grid grid-cols-2 border-t border-b border-slate-200 dark:border-slate-800 py-4">
                            <p class="text-[#4c669a] dark:text-slate-400 text-sm font-normal">Upload Date</p>
                            <p class="text-[#0d121b] dark:text-white text-sm font-medium text-right" x-text="formatDate(activeImage?.created_at)"></p>
                        </div>
                    </div>

                    <!-- URL Action Section -->
                    <div class="mt-8">
                        <h3 class="text-[#0d121b] dark:text-white text-sm font-bold uppercase tracking-wider mb-3">Image URL</h3>
                        <div class="flex flex-col gap-3">
                            <div class="flex w-full items-stretch rounded-lg h-11">
                                <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 h-full px-4 text-sm font-normal truncate" readonly="" :value="activeImage?.url"/>
                            </div>
                            <button @click="copyUrl(activeImage?.url)" class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary h-11 px-4 text-white text-sm font-bold transition-opacity hover:opacity-90">
                                <span class="material-symbols-outlined text-[20px]">content_copy</span>
                                <span>Copy Direct Link</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 flex gap-3">
                    <button @click="deleteImage(activeImage?.id); modalOpen = false;" class="flex-1 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 py-2.5 text-sm font-medium hover:bg-red-100 transition-colors">
                        Delete File
                    </button>
                    <!-- Edit Action -->
                    <button @click="editImageOpen = true" class="flex-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-white py-2.5 text-sm font-medium hover:bg-slate-50 transition-colors">
                         Edit Metadata
                    </button>
                </div>
            </div>
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
        <div class="flex items-center justify-center bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full size-8">
            <span class="material-symbols-outlined text-[20px]">check</span>
        </div>
        <div class="flex flex-col">
            <p class="text-sm font-bold text-slate-800 dark:text-white" x-text="toastMessage || 'Success'"></p>
            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="toastSubMessage || ''"></p>
        </div>
        <button @click="showToast = false" class="ml-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    <!-- Upload by URL Modal -->
    <div x-show="uploadUrlOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-[2px]" style="display: none;" x-transition.opacity>
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-xl shadow-2xl overflow-hidden mx-4" @click.away="uploadUrlOpen = false">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upload Image by URL</h3>
                <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" @click="uploadUrlOpen = false">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Paste an image URL below. The system will download and save the image.
                </p>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="image-url">Image URL</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">link</span>
                        <input x-model="urlToUpload" @keyup.enter="handleUrlUpload()" class="w-full h-11 pl-10 pr-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-slate-900 dark:text-white" id="image-url" name="image-url" placeholder="https://example.com/image.jpg" type="text"/>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-500">
                        Supported formats: <span class="font-medium">JPG, PNG, WEBP</span> • Max file size: <span class="font-medium">5MB</span>
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button class="px-5 py-2.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" @click="uploadUrlOpen = false">
                    Cancel
                </button>
                <button @click="handleUrlUpload()" :disabled="urlUploading || !urlToUpload" class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-primary hover:bg-primary/90 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                     <template x-if="!urlUploading">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            <span>Download &amp; Save</span>
                        </span>
                    </template>
                    <template x-if="urlUploading">
                        <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </template>
                </button>
            </div>
        </div>
    </div>



    <!-- Edit Image Modal -->
    <div x-show="editImageOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition.opacity>
        <div class="bg-white dark:bg-slate-900 w-full max-w-5xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.away="editImageOpen = false">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-bold">Edit Image</h2>
                <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" @click="editImageOpen = false">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto flex flex-col md:flex-row">
                <!-- Left: Preview & Info -->
                <div class="w-full md:w-1/2 p-8 border-r border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50">
                    <div class="aspect-video rounded-lg overflow-hidden shadow-inner bg-slate-200 dark:bg-slate-800 mb-8 border border-slate-200 dark:border-slate-700 relative">
                         <div class="w-full h-full bg-contain bg-center absolute inset-0" :style="`background-repeat: no-repeat;background-image: url('${activeImage?.url}')`;"></div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Image Details</h3>
                        <div class="grid grid-cols-2 gap-y-4">
                            <div>
                                <p class="text-[11px] text-slate-500 font-medium">Filename</p>
                                <p class="text-sm font-medium truncate pr-4 dark:text-slate-200" x-text="activeImage?.filename"></p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-medium">File Size</p>
                                <p class="text-sm font-medium dark:text-slate-200" x-text="formatSize(activeImage?.size)"></p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-medium">MIME Type</p>
                                <p class="text-sm font-medium dark:text-slate-200" x-text="activeImage?.mime_type"></p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-500 font-medium">Uploaded</p>
                                <p class="text-sm font-medium dark:text-slate-200" x-text="formatDate(activeImage?.created_at)"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Form -->
                <div class="w-full md:w-1/2 p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Title</label>
                        <input class="w-full h-11 px-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="Enter image title..." type="text" value="Golden Sunset at Beach"/>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alt Text</label>
                         <!-- Binding to actual alt_text from DB if available, else placeholder -->
                        <input x-model="activeImage.alt_text" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none" placeholder="Describe for accessibility..." type="text"/>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Description/Notes</label>
                        <textarea class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none min-h-[100px] resize-none" placeholder="Add additional details or notes...">Captured during the summer trip to the coast. High dynamic range used.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tags</label>
                        <div class="flex flex-wrap items-center gap-2 p-2 min-h-11 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary/10 text-primary rounded-md text-xs font-bold">
                                <span>sunset</span>
                                <span class="material-symbols-outlined text-[14px] cursor-pointer">close</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-primary/10 text-primary rounded-md text-xs font-bold">
                                <span>beach</span>
                                <span class="material-symbols-outlined text-[14px] cursor-pointer">close</span>
                            </div>
                            <input class="flex-1 bg-transparent border-none focus:ring-0 text-sm h-7 min-w-[60px]" placeholder="Add..." type="text"/>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Visibility</span>
                            <span class="text-[11px] text-slate-500">Enable to make this image public</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input checked="" class="sr-only peer" type="checkbox"/>
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-900/50">
                <button class="px-5 h-10 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors" @click="editImageOpen = false">
                    Cancel
                </button>
                <button class="bg-primary hover:bg-primary/90 text-white px-6 h-10 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2" @click="saveImageDetails()">
                    <span class="material-symbols-outlined text-[20px]">check</span>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function imageManager() {
        return {
            images: [],
            nextPageUrl: null,
            loading: false,
            modalOpen: false,
            uploadUrlOpen: false,
            editImageOpen: false,
            urlToUpload: '',
            urlUploading: false,
            activeImage: null,
            showToast: false,
            toastMessage: '',
            toastSubMessage: '',
            
            // ... init ...

            saveImageDetails() {
                // Mock save functionality as DB fields for tags/title don't exist yet
                // Only alt_text is real
                this.notify('Success', 'Image details saved locally (Mock)');
                this.editImageOpen = false;
                
                // If we had an API endpoint:
                /*
                fetch(`/api/images/${this.activeImage.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({ alt_text: this.activeImage.alt_text })
                    // ...
                })
                */
            },

            init() {
                this.fetchImages('/api/images');
            },

            fetchImages(url) {
                if (!url) return;
                this.loading = true;
                
                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        if (res.current_page === 1) {
                            this.images = res.data;
                        } else {
                            this.images = [...this.images, ...res.data];
                        }
                        this.nextPageUrl = res.next_page_url;
                    })
                    .catch(err => {
                        console.error(err);
                        this.notify('Error', 'Failed to load images');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            loadMore() {
                this.fetchImages(this.nextPageUrl);
            },
            
            checkScroll(event) {
                // Infinite scroll logic if desired
                // if (event.target.scrollTop + event.target.clientHeight >= event.target.scrollHeight - 100) {
                //    if (this.nextPageUrl && !this.loading) this.loadMore();
                // }
            },

            viewImage(image) {
                this.activeImage = image;
                this.modalOpen = true;
            },

            copyUrl(url) {
                if (!url) return;
                navigator.clipboard.writeText(url).then(() => {
                    this.notify('Copied!', 'URL added to clipboard');
                });
            },

            handleUrlUpload() {
                if (!this.urlToUpload) return;
                
                this.urlUploading = true;

                fetch('/api/upload/image-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        url: this.urlToUpload,
                        alt_text: 'Imported from URL'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.notify('Success', 'Image downloaded and saved');
                        this.urlToUpload = '';
                        this.uploadUrlOpen = false;
                        // Refresh grid to show new image
                        this.images.unshift(data.data); 
                    } else {
                        this.notify('Error', data.message || 'Failed to download image');
                    }
                })
                .catch(err => {
                    this.notify('Error', 'System error occurred.');
                    console.error(err);
                })
                .finally(() => {
                    this.urlUploading = false;
                });
            },

            deleteImage(id) {
                if (!confirm('Are you sure you want to delete this image?')) return;

                fetch(`/api/images/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.images = this.images.filter(img => img.id !== id);
                        this.notify('Deleted!', 'Image has been removed');
                    } else {
                        this.notify('Error', data.message || 'Delete failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.notify('Error', 'System error during deletion');
                });
            },

            notify(message, subMessage) {
                this.toastMessage = message;
                this.toastSubMessage = subMessage;
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            },
            
            formatSize(bytes) {
                 if (bytes === 0) return '0 B';
                 const k = 1024;
                 const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                 const i = Math.floor(Math.log(bytes) / Math.log(k));
                 return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }
        }
    }
</script>
@endsection
