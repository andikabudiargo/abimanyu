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
        if (! Schema::hasTable('transfer_in')) {
            Schema::create('transfer_in', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code')->nullable();
                $table->string('supplier_code', 50)->nullable();
                $table->date('date')->nullable();
                $table->string('status')->default('Draft');
                $table->string('reference_number');
                $table->string('transfer_category');
                $table->unsignedBigInteger('from_location')->nullable();
                $table->text('note')->nullable();
                $table->string('qr_code_path')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code'], 'code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_in');
    }
};
