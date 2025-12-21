<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('code')->nullable();              // Mã hóa đơn (nếu có)
            $table->date('invoice_date')->nullable();        // Ngày lập hóa đơn
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng tiền
            $table->string('total_text')->nullable();        // Tổng tiền bằng chữ
            $table->text('note')->nullable();                // Ghi chú
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
