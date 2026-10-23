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
        if (! Schema::hasTable('stocks')) {
            Schema::create('stocks', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('article_code');
                $table->unsignedBigInteger('warehouse_id');
                $table->decimal('qty', 10, 4)->default('0.0000');
                $table->enum('status', ['empty', 'critical', 'normal', 'overload'])->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->index(['warehouse_id'], 'warehouse_id');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
