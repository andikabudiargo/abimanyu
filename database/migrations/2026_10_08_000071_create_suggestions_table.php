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
        if (! Schema::hasTable('suggestions')) {
            Schema::create('suggestions', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('ss_number')->nullable()->collation('utf8mb4_unicode_ci');
                $table->longText('categories')->collation('utf8mb4_bin');
                $table->string('theme')->collation('utf8mb4_unicode_ci');
                $table->integer('department');
                $table->date('discovery_date');
                $table->string('location')->collation('utf8mb4_unicode_ci');
                $table->text('background')->nullable()->collation('utf8mb4_unicode_ci');
                $table->text('root_cause')->nullable()->collation('utf8mb4_unicode_ci');
                $table->text('improvement_activity')->nullable()->collation('utf8mb4_unicode_ci');
                $table->text('evaluation_result')->nullable()->collation('utf8mb4_unicode_ci');
                $table->string('standardization')->nullable()->collation('utf8mb4_unicode_ci');
                $table->string('status')->default('draft')->collation('utf8mb4_unicode_ci');
                $table->integer('completion_step')->default(1);
                $table->unsignedBigInteger('reviewed_by_spv')->nullable();
                $table->timestamp('reviewed_at_spv')->nullable();
                $table->text('spv_note')->nullable()->collation('utf8mb4_unicode_ci');
                $table->unsignedBigInteger('scored_by_manager')->nullable();
                $table->timestamp('scored_at')->nullable();
                $table->decimal('score_total', 5, 2)->nullable();
                $table->decimal('reward_amount', 15, 2)->nullable();
                $table->text('manager_note')->nullable()->collation('utf8mb4_unicode_ci');
                $table->unsignedBigInteger('period_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->dateTime('draft_at')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->integer('acknowledge_by')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
