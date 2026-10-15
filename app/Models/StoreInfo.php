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
        'zalo_oa_id',
        'zalo_oa_url',
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
            'zalo_oa_id' => null,
            'zalo_oa_url' => null,
        ]);
    }

    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'name' => 'Cửa hàng vật liệu xây dựng',
            'phone' => null,
            'zalo_oa_url' => null,
        ]);
    }

    public function telHref(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->phone);

        return $digits ? 'tel:' . $digits : null;
    }

    public function zaloHref(): ?string
    {
        if ($this->zalo_oa_url) {
            return $this->zalo_oa_url;
        }

        if (!$this->phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->phone);

        return $digits ? 'https://zalo.me/' . $digits : null;
    }
}
