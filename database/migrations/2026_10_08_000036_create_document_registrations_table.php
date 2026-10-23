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
        if (! Schema::hasTable('document_registrations')) {
            Schema::create('document_registrations', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('registration_number', 20);
                $table->integer('department_id');
                $table->string('document_number')->nullable();
                $table->string('document_type', 100);
                $table->enum('submission_type', ['New Release', 'Revision', 'Obsolete']);
                $table->string('document_title', 500);
                $table->text('reason')->nullable();
                $table->boolean('need_4m')->default(0);
                $table->text('file_path');
                $table->text('file_4m_path')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->integer('returned_by')->nullable();
                $table->dateTime('returned_at')->nullable();
                $table->longText('returned_reason')->nullable()->charset('utf8mb4')->collation('utf8mb4_bin');
                $table->dateTime('rejected_at')->nullable();
                $table->string('rejected_reason')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('authorized_by')->nullable();
                $table->dateTime('authorized_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->enum('status', ['Submitted', 'Approved', 'Returned', 'Resubmitted', 'Under Review', 'Published', 'Rejected', 'Returned by SPV', 'Returned by MR'])->default('Submitted');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_registrations');
    }
};
