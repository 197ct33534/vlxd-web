<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Tên khách hàng
            $table->string('phone')->nullable();    // SĐT
            $table->string('address')->nullable();  // Địa chỉ
            $table->text('note')->nullable();       // Ghi chú
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
