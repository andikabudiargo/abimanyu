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
        if (! Schema::hasTable('sto_items')) {
            Schema::create('sto_items', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('sto_id');
                $table->string('article_code', 100);
                $table->decimal('qty', 50, 2);
                $table->decimal('qty_2', 50, 2)->nullable();
                $table->string('kondisi', 50)->nullable();
                $table->string('location', 100);
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->string('uom', 11)->nullable();
                $table->string('other_name')->nullable();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_items');
    }
};
