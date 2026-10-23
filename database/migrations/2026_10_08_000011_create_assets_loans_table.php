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
        if (! Schema::hasTable('assets_loans')) {
            Schema::create('assets_loans', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('asset_id');
                $table->integer('user_id');
                $table->text('purpose');
                $table->dateTime('date_loan');
                $table->dateTime('return_estimation');
                $table->dateTime('date_return')->nullable();
                $table->string('status', 100)->default('Pending');
                $table->string('condition_return', 100)->nullable();
                $table->text('condition_note')->nullable();
                $table->string('photo_after_return')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->text('rejected_reason')->nullable();
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
        Schema::dropIfExists('assets_loans');
    }
};
