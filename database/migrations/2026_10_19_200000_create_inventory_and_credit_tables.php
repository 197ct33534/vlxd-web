<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('tax_code')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('receipt_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_receipt_id')->constrained('stock_receipts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('type');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('balance_after', 12, 3)->default(0);
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_quantity', 12, 3)->default(0);
            $table->decimal('avg_cost', 15, 2)->default(0);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('credit_days')->default(0);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->integer('credit_days')->nullable();
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['invoice_id', 'payment_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('cash');
            $table->string('proof_image')->nullable();
            $table->string('reference_code')->nullable();
        });

        Schema::table('delivery_note_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('order_item_id')->constrained('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_note_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'proof_image', 'reference_code']);
        });

        Schema::dropIfExists('payment_allocations');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'credit_days']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'credit_days']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'avg_cost']);
        });

        Schema::dropIfExists('stock_moves');
        Schema::dropIfExists('stock_receipt_items');
        Schema::dropIfExists('stock_receipts');
        Schema::dropIfExists('suppliers');
    }
};
