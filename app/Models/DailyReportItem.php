<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'project_id',
        'product_name',
        'unit',
        'quantity',
        'price',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
