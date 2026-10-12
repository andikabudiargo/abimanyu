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
        if (! Schema::hasTable('article_types')) {
            Schema::create('article_types', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->string('code', 10);
                $table->string('name');
                $table->text('note')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->string('created_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->string('updated_by')->nullable();

                $table->primary(['id']);
                $table->unique(['code'], 'code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_types');
    }
};
