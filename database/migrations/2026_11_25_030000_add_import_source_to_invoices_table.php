<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->char('import_hash', 64)->nullable()->index();
            $table->char('import_source_key', 64)->nullable();
            $table->string('import_filename')->nullable();
            $table->string('import_sheet')->nullable();
            $table->unique(['project_id', 'import_hash']);
            $table->index(['project_id', 'import_source_key']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['import_hash']);
            $table->dropUnique(['project_id', 'import_hash']);
            $table->dropIndex(['project_id', 'import_source_key']);
            $table->dropColumn(['import_hash', 'import_source_key', 'import_filename', 'import_sheet']);
        });
    }
};
