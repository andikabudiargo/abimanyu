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
        if (! Schema::hasTable('transfer_out')) {
            Schema::create('transfer_out', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code');
                $table->string('status', 20)->default('Draft');
                $table->string('reference_number')->nullable();
                $table->date('date');
                $table->string('transfer_type');
                $table->text('note')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code'], 'transfer_out_code_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_out');
    }
};
