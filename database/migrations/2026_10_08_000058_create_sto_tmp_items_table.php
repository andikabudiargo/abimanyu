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
        if (! Schema::hasTable('sto_tmp_items')) {
            Schema::create('sto_tmp_items', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('sto_header_id')->nullable();
                $table->string('third_party')->nullable();
                $table->string('article_code', 50);
                $table->string('uom', 50)->nullable();
                $table->text('part_address')->nullable();
                $table->decimal('utuh', 50, 2)->nullable();
                $table->decimal('tidak_utuh', 50, 2)->nullable();
                $table->decimal('qty', 50, 2)->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_tmp_items');
    }
};
