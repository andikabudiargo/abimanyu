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
        if (! Schema::hasTable('capas')) {
            Schema::create('capas', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('audit_id')->nullable();
                $table->string('capa_number', 100)->nullable();
                $table->date('report_date')->nullable();
                $table->string('source_of_finding', 100);
                $table->string('category', 100);
                $table->integer('dept_id');
                $table->integer('dept_representative');
                $table->integer('dept_representative_2')->nullable();
                $table->text('detail_of_information');
                $table->text('problem');
                $table->string('status', 100)->default('Draft');
                $table->enum('new_capa_needed', ['yes', 'no'])->default('no');
                $table->text('new_capa_reason')->nullable();
                $table->text('mr_statement')->nullable();
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->integer('posted_by')->nullable();
                $table->dateTime('posted_at')->nullable();
                $table->integer('verified_by')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->integer('processed_by')->nullable();
                $table->dateTime('processed_at')->nullable();
                $table->integer('review_by')->nullable();
                $table->dateTime('review_at')->nullable();
                $table->integer('submitted_by')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->integer('authorized_by')->nullable();
                $table->dateTime('authorized_at')->nullable();
                $table->integer('returned_by')->nullable();
                $table->dateTime('returned_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
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
        Schema::dropIfExists('capas');
    }
};
