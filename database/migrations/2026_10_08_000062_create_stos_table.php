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
        if (! Schema::hasTable('stos')) {
            Schema::create('stos', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('sto_number', 100)->nullable();
                $table->string('warehouse', 100);
                $table->string('area')->nullable();
                $table->string('shelves')->nullable();
                $table->integer('created_by_2')->nullable();
                $table->text('note')->nullable();
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->unique(['sto_number'], 'sto_number');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('stos');
    }
};
