@php
    $brand = $storeInfo?->name ?? __('landing.meta_brand_fallback');
    $titlePage = $brand.' — '.__('landing.meta_title_suffix');
    $shareUrl = url('/');
    $desc = config('site.meta_description');
    if (blank($desc) && ! empty($storeInfo?->note)) {
        $desc = \Illuminate\Support\Str::limit(preg_replace('/\s+/u', ' ', strip_tags($storeInfo->note)), 200);
    }
    if (blank($desc)) {
        $desc = __('landing.meta_description_default');
    }
    $ogImage = config('site.og_image');
    $fbAppId = config('site.facebook_app_id');
@endphp
<title>{{ $titlePage }}</title>
<meta name="description" content="{{ $desc }}"/>
<link rel="canonical" href="{{ $shareUrl }}"/>
<meta property="og:type" content="website"/>
<meta property="og:site_name" content="{{ $brand }}"/>
<meta property="og:title" content="{{ $titlePage }}"/>
<meta property="og:description" content="{{ $desc }}"/>
<meta property="og:url" content="{{ $shareUrl }}"/>
<meta property="og:locale" content="vi_VN"/>
<meta property="og:image" content="{{ $ogImage }}"/>
<meta property="og:image:alt" content="{{ $titlePage }}"/>
@if($fbAppId)
<meta property="fb:app_id" content="{{ $fbAppId }}"/>
@endif
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="{{ $titlePage }}"/>
<meta name="twitter:description" content="{{ $desc }}"/>
<meta name="twitter:image" content="{{ $ogImage }}"/>
