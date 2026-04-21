<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'price',
        'is_active',
        'display_order',
    ];
}
