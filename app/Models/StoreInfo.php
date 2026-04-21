<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    protected $table = 'store_info';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'bank_account',
        'bank_name',
        'bank_owner',
        'note',
    ];

    /**
     * Single-row config: ensure a record exists for admin forms.
     */
    public static function getOrCreateDefault(): self
    {
        $row = self::first();
        if ($row) {
            return $row;
        }

        return self::create([
            'name' => 'Cửa hàng vật liệu xây dựng',
            'address' => null,
            'phone' => null,
            'bank_account' => null,
            'bank_name' => null,
            'bank_owner' => null,
            'note' => null,
        ]);
    }
}
