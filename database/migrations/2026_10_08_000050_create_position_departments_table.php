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
        if (! Schema::hasTable('position_departments')) {
            Schema::create('position_departments', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('position_id');
                $table->integer('dept_id');

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_departments');
    }
};
