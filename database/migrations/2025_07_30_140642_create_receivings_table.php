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
        if (! Schema::hasTable('receivings')) {
            Schema::create('receivings', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->unsignedBigInteger('purchase_order_id');
                $table->string('receiving_number', 50);
                $table->date('received_date');
                $table->string('supplier_code')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
                $table->string('delivery_order_number', 50)->nullable();
                $table->date('delivery_order_date')->nullable();
                $table->integer('account_payable_id')->nullable();
                $table->integer('revision')->nullable()->default(0);
                $table->string('revision_reason')->nullable();
                $table->string('status')->nullable()->default('Draft');
                $table->string('note')->nullable();
                $table->string('qr_code')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->integer('created_by');
                $table->dateTime('checked_at')->nullable();
                $table->integer('checked_by')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->integer('verified_by')->nullable();
                $table->dateTime('updated_at')->nullable()->useCurrent();

                $table->primary(['id']);
                $table->index(['purchase_order_id'], 'purchase_order_id');
                $table->index(['supplier_code'], 'supplier_code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
