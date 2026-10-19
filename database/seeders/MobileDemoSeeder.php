<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Dữ liệu tối thiểu để test portal /m + admin orders + driver + giá realtime.
 * An toàn chạy lại (idempotent theo email/phone cố định).
 */
class MobileDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@vlxd.test'],
            [
                'name' => 'Admin VLXD',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'driver@vlxd.test'],
            [
                'name' => 'Tài xế Demo',
                'password' => Hash::make('password'),
                'role' => 'driver',
            ]
        );

        $customer = Customer::query()->updateOrCreate(
            ['phone' => '0901234567'],
            [
                'name' => 'Thầu Demo',
                'address' => 'Công trường demo',
                'status' => 'active',
            ]
        );

        $project = Project::query()->firstOrCreate(
            [
                'customer_id' => $customer->id,
                'name' => 'Công trình Demo A',
            ],
            [
                'address' => '123 Đường Công Trường, TP.HCM',
                'total_invoice' => 0,
                'total_paid' => 0,
                'total_debt' => 0,
            ]
        );

        $invoice = Invoice::query()->firstOrCreate(
            ['project_id' => $project->id, 'code' => 'HD-DEMO-001'],
            [
                'invoice_date' => now()->subDays(7)->toDateString(),
                'total_amount' => 0,
                'total_text' => null,
                'note' => 'Seed demo giá realtime',
            ]
        );

        if ($invoice->items()->count() === 0) {
            $lines = [
                ['product_name' => 'Xi măng PCB40', 'unit' => 'bao', 'quantity' => 50, 'price' => 92000],
                ['product_name' => 'Cát vàng', 'unit' => 'm3', 'quantity' => 10, 'price' => 450000],
                ['product_name' => 'Đá 1x2', 'unit' => 'm3', 'quantity' => 8, 'price' => 380000],
                ['product_name' => 'Sắt phi 10', 'unit' => 'kg', 'quantity' => 200, 'price' => 16500],
                ['product_name' => 'Gạch ống 4 lỗ', 'unit' => 'viên', 'quantity' => 1000, 'price' => 3200],
            ];

            $total = 0;
            foreach ($lines as $line) {
                $amount = $line['quantity'] * $line['price'];
                $total += $amount;
                $item = new InvoiceItem();
                $item->forceFill([
                    'invoice_id' => $invoice->id,
                    'date' => $invoice->invoice_date,
                    'product_name' => $line['product_name'],
                    'unit' => $line['unit'],
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'amount' => $amount,
                ])->save();
            }

            $invoice->update(['total_amount' => $total]);
            app(\App\Services\ProjectDebtService::class)->recalculate($project->id);
        }
    }
}
