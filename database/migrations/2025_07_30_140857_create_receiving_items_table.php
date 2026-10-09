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
        if (! Schema::hasTable('receiving_items')) {
            Schema::create('receiving_items', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('receiving_id');
                $table->unsignedBigInteger('po_item_id');
                $table->integer('destination_id')->nullable();
                $table->string('article_code')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
                $table->integer('qty_po')->default(0);
                $table->integer('qty_received')->default(0);
                $table->integer('qty_free')->nullable()->default(0);
                $table->integer('qty_used')->nullable()->default(0);
                $table->integer('qty_return')->nullable()->default(0);
                $table->integer('qty_total')->default(0);
                $table->date('expired_date')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->index(['article_code'], 'article_code');
                $table->index(['po_item_id'], 'po_item_id');
                $table->index(['receiving_id'], 'receiving_id');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_items');
    }
};
