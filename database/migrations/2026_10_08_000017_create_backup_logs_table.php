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
        if (! Schema::hasTable('backup_logs')) {
            Schema::create('backup_logs', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('backup_plan_id');
                $table->string('status', 50);
                $table->date('backup_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->decimal('final_size', 50, 2);
                $table->text('evidence');
                $table->text('remark');
                $table->integer('created_by');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
