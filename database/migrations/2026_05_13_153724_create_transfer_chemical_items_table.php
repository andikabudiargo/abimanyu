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
        if (! Schema::hasTable('transfer_chemical_items')) {
            Schema::create('transfer_chemical_items', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->unsignedBigInteger('transfer_chemical_id');
                $table->string('article_code', 20);
                $table->enum('condition', ['Utuh', 'Tidak Utuh']);
                $table->decimal('qty', 10, 2);
                $table->string('unit', 20);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_chemical_items');
    }
};
