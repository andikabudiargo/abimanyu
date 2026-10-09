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
        if (! Schema::hasTable('atk_requests')) {
            Schema::create('atk_requests', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('request_number', 50);
                $table->integer('department');
                $table->enum('status', ['submitted', 'approved', 'rejected', 'distributed', 'received'])->default('submitted');
                $table->text('note')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->integer('created_by');
                $table->dateTime('approved_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->dateTime('distributed_at')->nullable();
                $table->integer('distributed_by')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->integer('received_by')->nullable();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->unique(['request_number'], 'request_number');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_requests');
    }
};
