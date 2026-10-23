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
        if (! Schema::hasTable('armada')) {
            Schema::create('armada', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('bbm_id');
                $table->string('nama_armada');
                $table->string('rasio', 50);
                $table->decimal('spare', 50, 2);
                $table->string('icon', 50);
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
        Schema::dropIfExists('armada');
    }
};
