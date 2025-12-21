<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);     // Số tiền thanh toán
            $table->enum('payment_type', ['mot_lan', 'tung_dot'])->default('tung_dot'); // Hình thức
            $table->string('for_project')->nullable();         // Ghi chú: cho 1 công trình hay toàn bộ
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
