<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;
    public const DEBT_ADJUSTMENT = 'debt_adjustment';
    protected $fillable = [
        'project_id',
        'payment_date',
        'amount',
        'note',
        'payment_type',
        'for_project',
        'payment_method',
        'proof_image',
        'reference_code',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function isDebtAdjustment(): bool
    {
        return $this->payment_method === self::DEBT_ADJUSTMENT;
    }

    public function scopeReceipts(Builder $query): Builder
    {
        return $query->where('payment_method', '!=', self::DEBT_ADJUSTMENT);
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'payment_allocations')
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Enum hiện tại chỉ nhận mot_lan | tung_dot. Giá trị lạ (ung_truoc, manual) về tung_dot.
     */
    public static function normalizeType(?string $type): string
    {
        return in_array($type, ['mot_lan', 'tung_dot'], true) ? $type : 'tung_dot';
    }
}
