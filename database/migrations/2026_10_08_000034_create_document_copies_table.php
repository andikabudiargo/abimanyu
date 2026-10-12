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
        if (! Schema::hasTable('document_copies')) {
            Schema::create('document_copies', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('document_id')->nullable();
                $table->integer('registration_id');
                $table->string('department_id', 100);
                $table->integer('qty');
                $table->string('size', 10)->nullable();
                $table->integer('received_by')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->date('socialization_date')->nullable();
                $table->integer('socialized_by')->nullable();
                $table->text('evidence_path')->nullable();
                $table->boolean('copies_taken')->default(0);
                $table->unsignedBigInteger('copies_taken_from')->nullable();
                $table->timestamp('copies_taken_at')->nullable();
                $table->string('taken_evidence')->nullable();
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
        Schema::dropIfExists('document_copies');
    }
};
