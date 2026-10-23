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
        if (! Schema::hasTable('transfer_out_items')) {
            Schema::create('transfer_out_items', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('transfer_out_id');
                $table->unsignedBigInteger('transfer_in_item_code')->nullable();
                $table->string('transfer_in_code')->nullable();
                $table->string('article_code');
                $table->string('description');
                $table->decimal('qty', 10, 4)->default('0.0000');
                $table->string('uom')->nullable();
                $table->integer('min_package')->nullable();
                $table->date('expired_date')->nullable();
                $table->string('from_location')->nullable();
                $table->string('destination')->nullable();
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
        Schema::dropIfExists('transfer_out_items');
    }
};
