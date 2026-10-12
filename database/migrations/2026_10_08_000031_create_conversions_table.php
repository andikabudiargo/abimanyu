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
        if (! Schema::hasTable('conversions')) {
            Schema::create('conversions', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('conversion_number', 100);
                $table->integer('month');
                $table->string('status', 50);
                $table->decimal('total_qty', 50, 2);
                $table->decimal('total_conversion', 50, 2);
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('updated_at')->useCurrent();
                $table->year('year');
                $table->text('note');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
