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
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code');
                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('user_id');
                $table->text('description')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code'], 'categories_code_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
