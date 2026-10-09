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
        if (! Schema::hasTable('sj_temporary')) {
            Schema::create('sj_temporary', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->date('delivery_date');
                $table->string('customer');
                $table->string('article_code');
                $table->string('article_desc');
                $table->integer('delivery_qty');
                $table->decimal('price', 50, 2);
                $table->decimal('service_price', 50, 2);
                $table->decimal('grand_total', 50, 2);
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
        Schema::dropIfExists('sj_temporary');
    }
};
