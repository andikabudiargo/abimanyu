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
       Schema::create('receivings', function (Blueprint $table) {
    $table->id();
    $table->string('receiving_number')->unique();
    $table->date('received_date');
    $table->foreignId('purchase_order_id')->constrained();
    $table->string('supplier_code');
    $table->string('delivery_order_number');
    $table->date('delivery_order_date');
    $table->text('note')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
