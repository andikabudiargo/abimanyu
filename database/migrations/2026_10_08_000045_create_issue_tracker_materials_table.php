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
        if (! Schema::hasTable('issue_tracker_materials')) {
            Schema::create('issue_tracker_materials', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('issue_tracker_id');
                $table->string('material')->nullable();
                $table->integer('qty')->nullable();
                $table->string('uom', 10)->nullable();
                $table->string('vendor')->nullable();
                $table->integer('price')->nullable();
                $table->integer('subtotal')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_tracker_materials');
    }
};
