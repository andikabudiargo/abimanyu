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
        if (! Schema::hasTable('purchase_requests')) {
            Schema::create('purchase_requests', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('request_number');
                $table->integer('revision')->default(0);
                $table->date('request_date');
                $table->string('status', 50)->default('Draft');
                $table->string('order_type', 50);
                $table->date('stock_needed_at')->nullable();
                $table->unsignedBigInteger('sales_order_id')->nullable();
                $table->unsignedBigInteger('ga_request_id')->nullable();
                $table->text('pr_note')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('authorized_by')->nullable();
                $table->dateTime('authorized_at')->nullable();
                $table->integer('verified_by')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->integer('rejected_by')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['request_number'], 'purchase_requests_request_number_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
