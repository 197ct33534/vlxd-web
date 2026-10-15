<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_info', function (Blueprint $table) {
            $table->string('zalo_oa_id')->nullable()->after('note');
            $table->string('zalo_oa_url')->nullable()->after('zalo_oa_id');
        });
    }

    public function down(): void
    {
        Schema::table('store_info', function (Blueprint $table) {
            $table->dropColumn(['zalo_oa_id', 'zalo_oa_url']);
        });
    }
};
