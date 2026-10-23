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
        if (! Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code');
                $table->string('initial');
                $table->string('name');
                $table->string('type')->default('manufacture');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code'], 'departments_code_unique');
                $table->unique(['initial'], 'departments_initial_unique');
                $table->unique(['name'], 'departments_name_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
