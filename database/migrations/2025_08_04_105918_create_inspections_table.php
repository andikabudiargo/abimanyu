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
        if (! Schema::hasTable('inspections')) {
            Schema::create('inspections', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('inspection_number', 55);
                $table->unsignedBigInteger('user_id');
                $table->string('spraybooth', 50)->nullable();
                $table->string('shift')->nullable();
                $table->date('inspection_date')->nullable();
                $table->string('part_name');
                $table->string('supplier_code', 50)->nullable();
                $table->enum('inspection_post', ['Incoming', 'Unloading', 'Buffing', 'Touch Up', 'Final', 'Outgoing']);
                $table->string('check_method')->nullable();
                $table->text('note')->nullable();
                $table->integer('total_check');
                $table->integer('qty_received')->nullable();
                $table->integer('total_ok')->default(0);
                $table->integer('total_ok_repair')->default(0);
                $table->integer('total_ng')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['supplier_code'], 'supplier');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
