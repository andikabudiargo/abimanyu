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
        if (! Schema::hasTable('basic_prices')) {
            Schema::create('basic_prices', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('article_code', 50);
                $table->decimal('purchase_price', 50, 2);
                $table->decimal('selling_price', 50, 2);
                $table->decimal('rm_conversion', 50, 20);
                $table->decimal('fg_conversion', 50, 20);
                $table->decimal('matome', 50, 20);
                $table->decimal('conversion_value_used', 50, 2);
                $table->dateTime('last_calculated_at');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_prices');
    }
};
