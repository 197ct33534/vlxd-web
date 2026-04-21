<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ __('auth.login_title') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Theme Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2a8ff4",
                        "background-light": "#f5f7f8",
                        "background-dark": "#101922",
                        "card-dark": "#182430",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#111418] dark:text-white transition-colors duration-200">
    <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute top-[40%] -right-[10%] w-[40%] h-[60%] bg-primary/5 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Main Content -->
    <div class="relative z-10 flex flex-1 flex-col items-center justify-center p-4">
        <!-- Login Card -->
        <div class="w-full max-w-[440px] bg-white dark:bg-card-dark rounded-xl shadow-lg border border-[#dbe0e6] dark:border-[#2a3541] overflow-hidden">
            <!-- Header Section -->
            <div class="px-8 pt-10 pb-4 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-6 text-primary">
                    <span class="material-symbols-outlined !text-4xl">admin_panel_settings</span>
                </div>
                <h1 class="text-[#111418] dark:text-white text-3xl font-black leading-tight tracking-[-0.033em] mb-2">{{ __('auth.login') }}</h1>
                <p class="text-[#60758a] dark:text-[#9aaab9] text-base font-normal leading-normal">
                    {{ __('auth.login_subtitle') }}
                </p>
            </div>
            
            <!-- Form Section -->
            <form method="POST" action="{{ route('authenticate') }}" class="px-8 py-4 flex flex-col gap-5">
                @csrf
                
                <!-- Email/Username Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal">
                        {{ __('auth.email_address') }}
                    </label>
                    <div class="group flex w-full items-stretch rounded-lg border border-[#dbe0e6] dark:border-[#2a3541] bg-white dark:bg-[#111922] transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border-none bg-transparent h-12 px-4 text-[#111418] dark:text-white placeholder:text-[#60758a] dark:placeholder:text-[#5a6b7c] focus:outline-0 focus:ring-0 text-base font-normal leading-normal" 
                               placeholder="{{ __('auth.email_placeholder') }}" />
                        <div class="flex items-center justify-center px-4 text-[#60758a] dark:text-[#9aaab9]">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal">
                        {{ __('auth.password') }}
                    </label>
                    <div class="group flex w-full items-stretch rounded-lg border border-[#dbe0e6] dark:border-[#2a3541] bg-white dark:bg-[#111922] transition-colors focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                        <input type="password" name="password" id="password" required
                               class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border-none bg-transparent h-12 px-4 text-[#111418] dark:text-white placeholder:text-[#60758a] dark:placeholder:text-[#5a6b7c] focus:outline-0 focus:ring-0 text-base font-normal leading-normal" 
                               placeholder="{{ __('auth.password_placeholder') }}" />
                        <div id="togglePassword" class="flex items-center justify-center px-4 text-[#60758a] dark:text-[#9aaab9] cursor-pointer hover:text-[#111418] dark:hover:text-white transition-colors">
                            <span id="toggleIcon" class="material-symbols-outlined">visibility</span>
                        </div>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-1">
                    <div class="flex items-center gap-2">
                        <div class="relative flex items-center">
                            <input name="remember" type="checkbox"
                                   class="peer h-5 w-5 cursor-pointer appearance-none rounded border-2 border-[#dbe0e6] dark:border-[#4a5568] bg-white dark:bg-[#111922] checked:border-primary checked:bg-primary hover:border-primary/50 focus:outline-none focus:ring-0 focus:ring-offset-0" />
                            <span class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill-rule="evenodd"></path>
                                </svg>
                            </span>
                        </div>
                        <label class="text-[#111418] dark:text-gray-300 text-sm font-normal cursor-pointer select-none">{{ __('auth.remember_me') }}</label>
                    </div>
                    {{-- <a class="text-sm font-medium text-primary hover:text-primary/80 transition-colors" href="#">Forgot Password?</a> --}}
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-5 bg-primary hover:bg-primary/90 transition-colors text-white text-base font-bold leading-normal tracking-[0.015em] shadow-md mt-2">
                    <span class="material-symbols-outlined text-xl">login</span>
                    <span>{{ __('auth.login') }}</span>
                </button>
            </form>
            
            <!-- Footer Section of Card -->
            {{-- <div class="px-8 py-6 bg-gray-50 dark:bg-[#131d27] border-t border-[#dbe0e6] dark:border-[#2a3541] text-center">
                <p class="text-[#60758a] dark:text-[#9aaab9] text-sm">
                    Don't have an account? <span class="text-primary font-medium">Contact Admin</span>
                </p>
            </div> --}}
        </div>
        
        <!-- Page Footer -->
                    {{-- <div class="mt-8 text-center">
                        <p class="text-[#9aaab9] dark:text-[#5a6b7c] text-xs">
                            © 2023 Customer Management System. All rights reserved.
                        </p>
                    </div> --}}
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const toggleIcon = document.querySelector('#toggleIcon');

            if (togglePassword && password && toggleIcon) {
                togglePassword.addEventListener('click', function() {
                    // toggle the type attribute
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // toggle the icon
                    toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
                });
            }
        });
    </script>
</body>
</html>