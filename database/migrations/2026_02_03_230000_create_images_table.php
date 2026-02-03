<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Tên file gốc
            $table->string('path'); // Đường dẫn lưu trữ (ví dụ: images/2026/02/abc.jpg)
            $table->unsignedBigInteger('size'); // Kích thước file (bytes)
            $table->string('mime_type'); // Loại file (image/jpeg, image/png...)
            $table->string('alt_text')->nullable(); // Alt text cho SEO
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Người upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
