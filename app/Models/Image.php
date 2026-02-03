<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'size',
        'mime_type',
        'alt_text',
        'user_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * Get the URL of the image.
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }
    
    /**
     * Get the user who uploaded the image.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
