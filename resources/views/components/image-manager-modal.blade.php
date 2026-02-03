@props(['id' => 'imageManagerModal'])

<div x-data="imageManagerModal('{{ $id }}')"
     x-on:open-image-manager.window="if ($event.detail.targetId === '{{ $id }}') { openModal($event.detail); }"
     class="relative z-[100]"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;"
     x-show="isOpen">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <!-- Modal Panel -->
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-5xl h-[80vh] flex flex-col">
                
                <!-- Header -->
                <div class="bg-white dark:bg-slate-900 px-4 py-3 sm:px-6 border-b dark:border-slate-800 flex justify-between items-center shrink-0">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Select Image</h3>
                    <div class="flex items-center gap-3">
                         <!-- Search -->
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            <input type="text" placeholder="Search..." class="pl-9 pr-3 py-1.5 text-sm border rounded-md dark:bg-slate-800 dark:border-slate-700 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                        <button @click="closeModal()" type="button" class="text-gray-400 hover:text-gray-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                </div>

                <!-- Body (Grid) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50 dark:bg-slate-950/50" @scroll="checkScroll($event)">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <template x-for="image in images" :key="image.id">
                            <div class="group relative aspect-square bg-gray-200 dark:bg-slate-800 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-primary transition-all"
                                 @click="selectImage(image)">
                                <img :src="image.url" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-xs font-bold px-2 py-1 bg-primary rounded">Select</span>
                                </div>
                            </div>
                        </template>
                    </div>
                     <!-- Loading / Empty States -->
                     <template x-if="loading">
                        <div class="py-8 text-center text-gray-500">Loading images...</div>
                    </template>
                    <template x-if="!loading && images.length === 0">
                        <div class="py-8 text-center text-gray-500">No images found.</div>
                    </template>
                </div>

                <!-- Footer (Upload logic could go here) -->
                <div class="bg-gray-50 dark:bg-slate-900 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t dark:border-slate-800 shrink-0">
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-slate-800 dark:text-white dark:ring-slate-700 dark:hover:bg-slate-700" @click="closeModal()">Cancel</button>
                    <div class="mr-auto text-xs text-gray-500 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        <span>Click an image to select it.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageManagerModal', (id) => ({
            id: id,
            isOpen: false,
            images: [],
            nextPageUrl: null,
            loading: false,
            emitterId: null, // Who requested the image

            init() {
                // Pre-load or load on open
            },

            openModal(detail) {
                this.isOpen = true;
                this.emitterId = detail.emitterId; // allow reply back to specific picker
                if (this.images.length === 0) {
                    this.fetchImages('/api/images');
                }
            },

            closeModal() {
                this.isOpen = false;
            },

            fetchImages(url) {
                if (!url) return;
                this.loading = true;
                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        this.images = this.images.concat(res.data);
                        this.nextPageUrl = res.next_page_url;
                    })
                    .catch(console.error)
                    .finally(() => { this.loading = false; });
            },

            checkScroll(e) {
                if (this.loading || !this.nextPageUrl) return;
                const bottom = e.target.scrollHeight - e.target.scrollTop === e.target.clientHeight;
                if (bottom) {
                    this.fetchImages(this.nextPageUrl);
                }
            },

            selectImage(image) {
                // Dispatch event to Alpine components listening on window
                this.$dispatch('image-selected', {
                    url: image.url,
                    id: image.id,
                    emitterId: this.emitterId
                });
                this.closeModal();
            }
        }));
    });
</script>
