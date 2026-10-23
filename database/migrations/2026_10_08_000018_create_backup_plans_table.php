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
        if (! Schema::hasTable('backup_plans')) {
            Schema::create('backup_plans', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('job_name');
                $table->string('backup_type', 100);
                $table->integer('source');
                $table->integer('target');
                $table->string('frequency', 100);
                $table->integer('retention_policy');
                $table->integer('pic');
                $table->dateTime('updated_at')->useCurrent();
                $table->dateTime('created_at')->useCurrent();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_plans');
    }
};
