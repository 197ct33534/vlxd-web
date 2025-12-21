<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->date('date')->nullable();             // Ngày xuất hàng (VD: 26/9/2025)
            $table->string('product_name');               // Tên vật liệu
            $table->string('unit')->nullable();           // Đơn vị tính (bao, xe, viên,...)
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
