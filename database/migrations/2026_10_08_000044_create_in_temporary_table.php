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
        if (! Schema::hasTable('in_temporary')) {
            Schema::create('in_temporary', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->integer('id')->autoIncrement();
                $table->date('date');
                $table->string('article_code', 50);
                $table->string('article_desc', 200);
                $table->integer('qty');
                $table->string('supplier', 10);
                $table->string('location_from', 100);
                $table->string('location_to', 100);
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
        Schema::dropIfExists('in_temporary');
    }
};
