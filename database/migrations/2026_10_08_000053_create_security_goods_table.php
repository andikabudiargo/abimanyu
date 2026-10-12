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
        if (! Schema::hasTable('security_goods')) {
            Schema::create('security_goods', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('jenis_barang', 50);
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_keluar')->nullable();
                $table->string('perusahaan')->nullable();
                $table->string('nama_pengirim');
                $table->string('identitas', 50)->nullable();
                $table->string('nomor_kendaraan', 50)->nullable();
                $table->string('nama_penerima');
                $table->text('surat_jalan')->nullable();
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
        Schema::dropIfExists('security_goods');
    }
};
