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
        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('registration_id');
                $table->string('document_number', 100)->nullable();
                $table->string('document_type', 100);
                $table->enum('remark', ['New Release', 'Revision', 'Obsolete']);
                $table->string('document_title', 500);
                $table->integer('current_version');
                $table->text('file_path');
                $table->text('file_4m_path')->nullable();
                $table->integer('dept_from');
                $table->integer('dept_to');
                $table->tinyInteger('is_active')->default(1);
                $table->integer('submitted_by');
                $table->dateTime('submitted_at');
                $table->integer('published_by');
                $table->dateTime('published_at');
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
        Schema::dropIfExists('documents');
    }
};
