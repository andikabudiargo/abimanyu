<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('dept_id'); // ID dari departemen
            $table->date('request_date');
            $table->enum('order_type', ['standard', 'ga_request', 'sales_order']);
            $table->date('stock_needed_at')->nullable(); // khusus sales_order
            $table->unsignedBigInteger('sales_order_id')->nullable(); // jika pakai relasi
            $table->unsignedBigInteger('ga_request_id')->nullable(); // jika pakai relasi
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
}
