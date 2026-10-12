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
        if (! Schema::hasTable('apd_adjustments')) {
            Schema::create('apd_adjustments', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('transaction_code');
                $table->string('reference_number')->nullable();
                $table->date('adjustment_date');
                $table->enum('adjustment_type', ['IN', 'OUT']);
                $table->text('adjustment_reason');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->unique(['transaction_code'], 'transaction_code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('apd_adjustments');
    }
};
