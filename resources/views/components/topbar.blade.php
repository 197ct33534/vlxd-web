@props(['heading' => null])
<header class="flex-shrink-0 border-b border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
    <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
        <div class="flex min-w-0 flex-1 items-center gap-2">
            <button
                type="button"
                class="flex flex-shrink-0 items-center gap-1.5 rounded-lg px-2 py-2 text-text-light hover:bg-primary/10 dark:text-text-dark lg:hidden"
                @click="$store.layout.toggleSidebar()"
            >
                <span class="material-symbols-outlined text-2xl">menu</span>
                <span class="text-xs font-bold">{{ __('nav.open_menu') }}</span>
            </button>
            <div class="min-w-0">
                <h2 class="truncate text-base font-bold tracking-tight text-text-light dark:text-text-dark sm:text-lg">
                    {{ $heading ?? 'Quản trị' }}
                </h2>
                <p class="hidden truncate text-xs text-gray-500 dark:text-gray-400 sm:block">
                    {{ Auth::user()->name ?? '' }} @if(Auth::user())<span class="capitalize">· {{ Auth::user()->role }}</span>@endif
                </p>
            </div>
        </div>
        <div class="flex flex-shrink-0 items-center gap-2 sm:gap-4">
            <label class="relative hidden md:block">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">search</span>
                <input class="form-input h-10 w-52 rounded-lg border-none bg-background-light pl-10 placeholder:text-gray-400 focus:ring-2 focus:ring-primary/50 dark:bg-background-dark dark:placeholder:text-gray-500 lg:w-64" placeholder="Tìm kiếm..." type="search"/>
            </label>
            <button type="button" class="relative inline-flex max-w-[140px] items-center gap-1.5 rounded-full py-2 pl-3 pr-2 text-text-light hover:bg-primary/10 dark:text-text-dark sm:max-w-none">
                <span class="material-symbols-outlined shrink-0">notifications</span>
                <span class="truncate text-xs font-semibold">{{ __('nav.notifications') }}</span>
                <span class="absolute right-1 top-1 block h-2 w-2 rounded-full bg-red-500"></span>
            </button>
            <div class="hidden h-8 w-px bg-gray-200 dark:bg-gray-700 sm:block"></div>

            <div class="flex items-center gap-2" x-data="{ open: false }">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs capitalize text-gray-500 dark:text-gray-400">{{ Auth::user()->role ?? 'Employee' }}</p>
                </div>
                <div class="relative">
                    <button type="button" @click="open = !open" @click.away="open = false" class="flex h-10 max-w-[120px] items-center justify-center gap-1.5 rounded-full bg-primary/10 px-2 text-primary hover:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 sm:max-w-none sm:px-3">
                        <span class="material-symbols-outlined shrink-0">person</span>
                        <span class="truncate text-xs font-bold">{{ __('nav.account_menu') }}</span>
                    </button>

                    <div
                        x-show="open"
                        x-transition
                        x-cloak
                        class="absolute right-0 z-50 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800"
                        style="display: none;"
                    >
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
