<header class="flex-shrink-0 bg-container-light dark:bg-container-dark border-b border-border-light dark:border-border-dark flex items-center justify-between px-6 py-3 shadow-subtle">
    <div class="flex items-center gap-8">
        <h2 class="text-text-light dark:text-text-dark text-lg font-bold tracking-tight">Customer Management</h2>
    </div>
    <div class="flex items-center gap-4">
        <label class="relative hidden md:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">search</span>
            <input class="form-input w-64 rounded-lg border-none bg-background-light dark:bg-background-dark pl-10 h-10 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-primary/50" placeholder="Search..." type="search"/>
        </label>
        <button class="relative rounded-full p-2 text-text-light dark:text-text-dark hover:bg-primary/10 transition-colors">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500"></span>
        </button>
        <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-2"></div>
            
        <div class="flex items-center gap-3" x-data="{ open: false }">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ Auth::user()->role ?? 'Employee' }}</p>
            </div>
            <div class="relative">
                <button @click="open = !open" @click.away="open = false" class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <span class="material-symbols-outlined">person</span>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        style="display: none;"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
