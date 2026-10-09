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
        if (! Schema::hasTable('lpb_temporary')) {
            Schema::create('lpb_temporary', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->date('do_date');
                $table->string('supplier_code');
                $table->text('supplier_name');
                $table->string('article_code');
                $table->text('article_name');
                $table->integer('qty');
                $table->string('uom', 50);
                $table->decimal('price', 50, 2);
                $table->decimal('total_tanpa_ppn', 50, 2);
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
        Schema::dropIfExists('lpb_temporary');
    }
};
