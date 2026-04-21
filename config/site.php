<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta & chia sẻ (Facebook Messenger, Zalo, v.v.)
    |--------------------------------------------------------------------------
    | SITE_OG_IMAGE: URL ảnh tuyệt đối (https://...), khuyến nghị 1200×630px.
    | Để trống SITE_META_DESCRIPTION sẽ dùng ghi chú cửa hàng hoặc chuỗi mặc định.
    */

    'meta_description' => env('SITE_META_DESCRIPTION'),

    'og_image' => env('SITE_OG_IMAGE', 'https://images.unsplash.com/photo-1597974380476-fbf652dfe188?fm=jpg&w=1200&h=630&fit=crop&q=80'),

    'facebook_app_id' => env('FACEBOOK_APP_ID'),
];
