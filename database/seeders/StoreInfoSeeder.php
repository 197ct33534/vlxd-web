<?php

namespace Database\Seeders;

use App\Models\StoreInfo;
use Illuminate\Database\Seeder;

class StoreInfoSeeder extends Seeder
{
    public function run(): void
    {
        if (StoreInfo::query()->exists()) {
            return;
        }

        StoreInfo::create([
            'name' => 'Cửa hàng vật liệu xây dựng',
            'phone' => '0900000000',
            'address' => null,
            'zalo_oa_url' => null,
        ]);
    }
}
