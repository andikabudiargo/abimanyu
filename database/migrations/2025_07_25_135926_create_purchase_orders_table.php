<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat struktur tabel sesuai skema dump produksi (abimany3_abimanyu_db).
     */
    public function up(): void
    {
        if (! Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('is_latest')->default(1);
                $table->integer('purchase_request_id')->nullable();
                $table->string('order_number')->nullable();
                $table->date('order_date');
                $table->date('delivery_date')->nullable();
                $table->string('supplier_code');
                $table->string('status', 50)->nullable()->default('Draft');
                $table->integer('revision')->nullable()->default(0);
                $table->string('revisi_reason')->nullable();
                $table->string('rejected_reason')->nullable();
                $table->integer('top')->nullable()->comment('Term of Payment in days');
                $table->boolean('pkp')->default(0)->comment('Taxable Person');
                $table->text('note')->nullable();
                $table->decimal('subtotal', 15, 2)->nullable()->default('0.00');
                $table->decimal('discount', 15, 2)->default('0.00');
                $table->boolean('use_ppn')->default(1);
                $table->boolean('use_pph')->default(0);
                $table->decimal('ppn', 15, 2)->nullable()->default('0.00');
                $table->decimal('pph', 15, 2)->nullable()->default('0.00');
                $table->decimal('netto', 15, 2)->nullable()->default('0.00');
                $table->string('created_by');
                $table->timestamp('created_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('reject_by')->nullable();
                $table->dateTime('reject_at')->nullable();
                $table->integer('authorized_by')->nullable();
                $table->dateTime('authorized_at')->nullable();
                $table->integer('verified_by')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['parent_id'], 'parent_id');
                $table->unique(['order_number'], 'purchase_orders_reference_number_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
