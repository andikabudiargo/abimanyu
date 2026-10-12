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
        if (! Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code');
                $table->string('name');
                $table->string('type')->nullable();
                $table->string('pic')->nullable();
                $table->enum('capacity', ['kosong', 'Normal', 'Penuh'])->nullable()->default('Normal');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('note')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('created_by')->nullable();

                $table->unique(['code'], 'warehouses_code_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
