<?php

return [
    'oa_access_token' => env('ZALO_OA_ACCESS_TOKEN'),
    'oa_id' => env('ZALO_OA_ID'),
    'zns_endpoint' => env('ZALO_ZNS_ENDPOINT', 'https://business.openapi.zalo.me/message/template'),
    'zns_templates' => [
        'confirmed' => env('ZALO_ZNS_TEMPLATE_CONFIRMED'),
        'delivering' => env('ZALO_ZNS_TEMPLATE_DELIVERING'),
        'delivered' => env('ZALO_ZNS_TEMPLATE_DELIVERED'),
    ],
];
