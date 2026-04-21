{{-- Form gửi SĐT khách → Telegram (LandingQuoteRequestController) --}}
@php $variant = $variant ?? 'hero'; @endphp

@if(session('quote_request_success'))
    <div class="mb-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow {{ $variant === 'hero' ? '' : 'dark:bg-emerald-700' }}">
        {{ __('landing.quote_success') }}
    </div>
@endif
@if($errors->has('phone'))
    <div class="mb-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-bold text-white shadow">
        {{ $errors->first('phone') }}
    </div>
@endif

@if($variant === 'hero')
    <form method="POST" action="{{ route('landing.quote-request') }}" class="bg-white rounded-xl p-2 flex flex-col sm:flex-row gap-2">
        @csrf
        <div class="flex-1 flex items-center px-4">
            <span class="material-symbols-outlined text-slate-400 mr-2">phone_iphone</span>
            <input
                name="phone"
                value="{{ old('phone') }}"
                type="tel"
                inputmode="tel"
                autocomplete="tel"
                required
                maxlength="20"
                class="w-full border-0 focus:ring-0 text-slate-900 font-medium placeholder:text-slate-400"
                placeholder="{{ __('landing.quote_phone_placeholder') }}"
            />
        </div>
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2 whitespace-nowrap">
            {{ __('landing.quote_submit') }}
            <span class="material-symbols-outlined">trending_flat</span>
        </button>
    </form>
@else
    <form method="POST" action="{{ route('landing.quote-request') }}" class="flex flex-col gap-3">
        @csrf
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">phone_iphone</span>
            <input
                name="phone"
                value="{{ old('phone') }}"
                type="tel"
                inputmode="tel"
                autocomplete="tel"
                required
                maxlength="20"
                class="w-full pl-10 pr-4 py-3.5 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 focus:ring-primary focus:border-primary"
                placeholder="{{ __('landing.quote_phone_placeholder') }}"
            />
        </div>
        <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
            <span>{{ __('landing.quote_submit_mobile') }}</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </button>
    </form>
@endif
