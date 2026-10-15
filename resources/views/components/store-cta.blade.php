@props(['store' => null])

@php
    $store = $store ?? \App\Models\StoreInfo::current();
    $tel = $store->telHref();
    $zalo = $store->zaloHref();
@endphp

@if($tel || $zalo)
<div class="mf-sticky-cta max-w-lg mx-auto">
    <div class="mf-cta-row">
        @if($tel)
            <a href="{{ $tel }}" class="mf-cta-btn mf-cta-btn--primary mf-touch">
                <span class="material-symbols-outlined text-xl">call</span>
                Gọi shop
            </a>
        @endif
        @if($zalo)
            <a href="{{ $zalo }}" target="_blank" rel="noopener" class="mf-cta-btn mf-cta-btn--secondary mf-touch">
                <span class="material-symbols-outlined text-xl">chat</span>
                Zalo
            </a>
        @endif
    </div>
</div>
@endif
