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
        if (! Schema::hasTable('purchase_order_request')) {
            Schema::create('purchase_order_request', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('purchase_request_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['purchase_order_id', 'purchase_request_id'], 'po_pr_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_request');
    }
};
