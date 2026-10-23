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
        if (! Schema::hasTable('security_good_items')) {
            Schema::create('security_good_items', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('security_good_id');
                $table->string('nama_barang');
                $table->integer('jumlah');
                $table->text('foto')->nullable();
                $table->string('kondisi', 50);
                $table->text('catatan')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_good_items');
    }
};
