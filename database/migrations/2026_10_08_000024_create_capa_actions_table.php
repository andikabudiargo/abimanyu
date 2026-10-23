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
        if (! Schema::hasTable('capa_actions')) {
            Schema::create('capa_actions', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('capa_id');
                $table->enum('type', ['RCA', 'CA', 'PA']);
                $table->text('description');
                $table->integer('pic')->nullable();
                $table->date('due_date')->nullable();
                $table->text('supporting_document')->nullable();
                $table->string('status', 50)->nullable()->default('Open');
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('capa_actions');
    }
};
