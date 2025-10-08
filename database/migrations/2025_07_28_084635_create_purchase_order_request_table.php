<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('purchase_order_request', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('purchase_order_id');
    $table->unsignedBigInteger('purchase_request_id');
    $table->timestamps();

    $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
    $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade');

    // Ganti nama constraint UNIQUE yang terlalu panjang
    $table->unique(['purchase_order_id', 'purchase_request_id'], 'po_pr_unique');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_request');
    }
};
