<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'phone', 'email', 'status', 'credit_limit', 'credit_days'];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function advances()
    {
        return $this->hasMany(CustomerAdvance::class);
    }

    public function advanceReceipts()
    {
        return $this->advances()->where('payment_method', '!=', Payment::DEBT_ADJUSTMENT);
    }

    public function projectReceipts()
    {
        return $this->hasManyThrough(Payment::class, Project::class)->receipts();
    }

    public function scopeWithAccountTotals(Builder $query): Builder
    {
        return $query->withSum('projects as account_invoiced', 'total_invoice')
            ->withSum('projects as account_project_paid', 'total_paid')
            ->withSum('advances as account_advanced', 'amount');
    }

    public function accountBalance(): float
    {
        return round((float) $this->account_invoiced - (float) $this->account_project_paid - (float) $this->account_advanced, 2);
    }

    public static function totalOutstandingDebt(): float
    {
        return (float) DB::query()->fromSub(static::withAccountTotals(), 'customer_accounts')
            ->selectRaw('SUM(CASE WHEN (COALESCE(account_invoiced, 0) - COALESCE(account_project_paid, 0) - COALESCE(account_advanced, 0)) > 0
                THEN COALESCE(account_invoiced, 0) - COALESCE(account_project_paid, 0) - COALESCE(account_advanced, 0) ELSE 0 END) AS total')
            ->value('total');
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Project::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
