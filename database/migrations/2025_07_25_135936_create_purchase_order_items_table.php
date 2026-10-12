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
        if (! Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('purchase_request_id');
                $table->string('article_code');
                $table->decimal('qty', 15, 2);
                $table->decimal('qty_received', 50, 2)->nullable()->default('0.00');
                $table->decimal('price', 15, 2);
                $table->decimal('total', 15, 2);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['article_code'], 'article_code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
