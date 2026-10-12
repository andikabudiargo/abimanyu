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
        if (! Schema::hasTable('document_revisions')) {
            Schema::create('document_revisions', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('document_id')->nullable();
                $table->integer('registration_id');
                $table->integer('revision_number')->nullable();
                $table->text('file_4m_path')->nullable();
                $table->text('file_path')->nullable();
                $table->text('before_change')->nullable();
                $table->text('after_change')->nullable();
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
        Schema::dropIfExists('document_revisions');
    }
};
