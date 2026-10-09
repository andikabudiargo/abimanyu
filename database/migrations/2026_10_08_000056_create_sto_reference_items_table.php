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
        if (! Schema::hasTable('sto_reference_items')) {
            Schema::create('sto_reference_items', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('sto_reference_id')->nullable();
                $table->string('article_code', 50);
                $table->decimal('qty', 50, 2)->nullable();
                $table->string('uom', 10)->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();
                $table->string('warehouse', 100)->nullable();
                $table->string('area', 100)->nullable();
                $table->string('shelves', 100)->nullable();
                $table->string('article_desc')->nullable();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_reference_items');
    }
};
