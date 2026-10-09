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
        if (! Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('ticket_number');
                $table->unsignedBigInteger('category_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('attachment')->nullable();
                $table->string('status')->default('Pending');
                $table->string('priority')->nullable()->default('Under Review');
                $table->unsignedBigInteger('request_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->string('reject_by', 40)->nullable();
                $table->dateTime('reject_at')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->text('corrective_action')->nullable();
                $table->text('preventive_action')->nullable();
                $table->string('evidence_path')->nullable();
                $table->dateTime('done_at')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->text('feedback')->nullable();

                $table->index(['category_id'], 'category_id');
                $table->index(['processed_by'], 'processed_by');
                $table->unique(['ticket_number'], 'tickets_ticket_number_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
