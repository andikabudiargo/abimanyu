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
    {Schema::create('receiving_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('receiving_id')->constrained();
    $table->foreignId('purchase_order_item_id')->constrained();
    $table->decimal('qty_received', 10, 2);
    $table->decimal('qty_free', 10, 2)->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_items');
    }
};
