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
        if (! Schema::hasTable('apds')) {
            Schema::create('apds', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('code', 100);
                $table->string('name');
                $table->string('conditions', 50);
                $table->integer('initial_stock')->default(0);
                $table->integer('min_stock')->default(0);
                $table->string('uom', 10)->default('PCS');
                $table->integer('lifetime')->default(12);
                $table->string('icon');
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
        Schema::dropIfExists('apds');
    }
};
