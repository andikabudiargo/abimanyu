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
        if (! Schema::hasTable('finger_logs')) {
            Schema::create('finger_logs', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->integer('id')->autoIncrement();
                $table->string('machine_id', 100)->nullable();
                $table->string('nik');
                $table->integer('status');
                $table->dateTime('timestamp');
                $table->longText('raw_data')->collation('utf8mb4_bin');
                $table->string('log_hash', 55)->nullable();
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->unique(['log_hash'], 'log_hash');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('finger_logs');
    }
};
