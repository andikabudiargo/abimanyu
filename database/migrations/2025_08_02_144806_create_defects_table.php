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
        if (! Schema::hasTable('defects')) {
            Schema::create('defects', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code', 50);
                $table->string('defect');
                $table->boolean('raw_material')->default(0);
                $table->string('description')->nullable();
                $table->enum('inspection_post', ['Incoming', 'Unloading', 'Buffing', 'Touch Up', 'Final', 'Outgoing'])->nullable();
                $table->enum('category', ['NG', 'NC', 'Both'])->nullable();
                $table->enum('status', ['active', 'inactive']);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('defects');
    }
};
