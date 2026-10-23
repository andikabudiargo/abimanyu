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
        if (! Schema::hasTable('transfer_in_items')) {
            Schema::create('transfer_in_items', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('transfer_in_id');
                $table->unsignedBigInteger('destination_id')->nullable();
                $table->string('code');
                $table->string('transfer_in_code')->nullable();
                $table->string('article_code');
                $table->string('description')->nullable();
                $table->decimal('qty', 10, 4);
                $table->decimal('qty_used', 10, 4)->nullable()->default('0.0000');
                $table->decimal('qty_return', 10, 4)->nullable()->default('0.0000');
                $table->integer('balance')->nullable();
                $table->string('uom')->nullable();
                $table->bigInteger('min_package')->nullable();
                $table->date('expired_date')->nullable();
                $table->string('qr_path')->nullable();
                $table->integer('origin_item_id')->nullable();
                $table->string('origin_type', 100)->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->nullable();

                $table->index(['article_code'], 'article_code');
                $table->index(['transfer_in_code'], 'transfer_in_code');
                $table->index(['transfer_in_id'], 'transfer_in_id');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_in_items');
    }
};
