<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectDebtService
{
    /**
     * Ghi lại total_invoice, total_paid, total_debt từ hóa đơn và phiếu thu.
     * Gọi ở cuối transaction đã ghi chứng từ.
     */
    public function recalculate(int $projectId): void
    {
        $query = Project::query()->whereKey($projectId);
        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        $project = $query->first();
        if (!$project) {
            return;
        }

        $totalInvoice = round((float) Invoice::query()->where('project_id', $projectId)->sum('total_amount'), 2);
        $totalPaid = round((float) Payment::query()->where('project_id', $projectId)->sum('amount'), 2);

        $project->forceFill([
            'total_invoice' => $totalInvoice,
            'total_paid' => $totalPaid,
            'total_debt' => max(0, round($totalInvoice - $totalPaid, 2)),
        ])->save();
    }
}
