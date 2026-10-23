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
        if (! Schema::hasTable('issue_trackers')) {
            Schema::create('issue_trackers', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('request_number', 50);
                $table->text('location_area');
                $table->string('request_type', 50);
                $table->text('description');
                $table->text('attachment')->nullable();
                $table->string('urgency', 50);
                $table->text('recommendation');
                $table->string('status', 50)->default('Pending');
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->string('rejected_reason')->nullable();
                $table->integer('checked_by')->nullable();
                $table->dateTime('checked_at')->nullable();
                $table->string('check_result')->nullable();
                $table->integer('duration_work')->nullable();
                $table->text('recommended_action')->nullable();
                $table->integer('verification_by')->nullable();
                $table->dateTime('verification_at')->nullable();
                $table->integer('authorized_by')->nullable();
                $table->dateTime('authorized_at')->nullable();
                $table->string('assigned_by')->nullable();
                $table->integer('done_by')->nullable();
                $table->dateTime('done_at')->nullable();
                $table->integer('closed_by')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->date('work_start')->nullable();
                $table->date('work_end')->nullable();
                $table->text('evidence_before')->nullable();
                $table->text('evidence_after')->nullable();
                $table->text('note_done')->nullable();
                $table->string('work_verification', 50)->nullable();
                $table->enum('confirmation', ['0', '1'])->nullable();
                $table->integer('rating')->nullable();
                $table->text('feedback')->nullable();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_trackers');
    }
};
