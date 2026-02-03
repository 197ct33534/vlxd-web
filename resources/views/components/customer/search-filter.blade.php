<div class="bg-container-light dark:bg-container-dark p-4 rounded-xl shadow-subtle mb-6"
     x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'all') }}',
        timeout: null,
        
        init() {
            $watch('search', value => {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.submitForm();
                }, 2000);
            });
            $watch('status', value => {
                this.submitForm();
            });
        },
        
        submitForm() {
            $refs.form.submit();
        },
        
        clearSearch() {
            this.search = '';
            // submitForm will be triggered by watcher, but for immediate feel:
            // logic handled by watcher, but 2s delay might be annoying for clear.
            // Let's force submit immediately on clear.
            clearTimeout(this.timeout); 
            $nextTick(() => this.submitForm());
        }
     }">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <form x-ref="form" action="{{ route('customers.index') }}" method="GET" class="flex flex-col w-full">
                <div class="relative flex w-full flex-1 items-stretch h-12">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">search</span>
                    <input x-model="search" name="search" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-light dark:text-text-dark focus:outline-0 focus:ring-2 focus:ring-primary/50 border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark h-full placeholder:text-gray-400 dark:placeholder:text-gray-500 pl-12 pr-12 text-base font-normal" placeholder="Search by name, phone, or company" />
                    
                    <!-- Clear Button -->
                    <button type="button" x-show="search.length > 0" @click="clearSearch()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <!-- Hidden input for status to be submitted with the form -->
                <input type="hidden" name="status" :value="status">
            </form>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative w-full">
                <select x-model="status" class="appearance-none w-full h-12 pl-4 pr-10 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 cursor-pointer">
                    <option value="all">Status: All</option>
                    <option value="active">Status: Active</option>
                    <option value="inactive">Status: Inactive</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                     <span class="material-symbols-outlined max-w-[20px]">expand_more</span>
                </div>
            </div>
        </div>
    </div>
</div>
