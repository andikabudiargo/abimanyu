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
        if (! Schema::hasTable('boms')) {
            Schema::create('boms', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('code');
                $table->string('customer');
                $table->string('article_fg');
                $table->string('article_fg_desc');
                $table->string('article_rm');
                $table->string('article_rm_desc')->charset('utf8mb4')->collation('utf8mb4_swedish_ci');
                $table->string('article_cm');
                $table->string('article_cm_desc')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
                $table->decimal('qty', 50, 5);
                $table->string('uom', 50);
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
        Schema::dropIfExists('boms');
    }
};
