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
        if (! Schema::hasTable('documents_backup_20261005')) {
            Schema::create('documents_backup_20261005', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->integer('id')->default(0);
                $table->integer('registration_id');
                $table->string('document_number', 100)->charset('latin1')->collation('latin1_swedish_ci');
                $table->string('document_type', 100)->charset('latin1')->collation('latin1_swedish_ci');
                $table->enum('remark', ['New Release', 'Revision', 'Obsolete'])->charset('latin1')->collation('latin1_swedish_ci');
                $table->string('document_title', 500)->charset('latin1')->collation('latin1_swedish_ci');
                $table->integer('current_version');
                $table->text('file_path')->charset('latin1')->collation('latin1_swedish_ci');
                $table->text('file_4m_path')->nullable()->charset('latin1')->collation('latin1_swedish_ci');
                $table->integer('dept_from');
                $table->integer('dept_to');
                $table->tinyInteger('is_active')->default(1);
                $table->integer('submitted_by');
                $table->dateTime('submitted_at');
                $table->integer('published_by');
                $table->dateTime('published_at');
                $table->dateTime('updated_at')->useCurrent();

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_backup_20261005');
    }
};
