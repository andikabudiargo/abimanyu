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
        if (! Schema::hasTable('suggestion_reward_formulas')) {
            Schema::create('suggestion_reward_formulas', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(1);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestion_reward_formulas');
    }
};
