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
        Schema::create('transfer_in', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->enum('transfer_type', ['incoming', 'mutasi', 'retur', 'transit']);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('from_location')->nullable();
            $table->unsignedBigInteger('to_location')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('from_location')->references('id')->on('warehouses')->onDelete('set null');
            $table->foreign('to_location')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_transfer_in');
    }
};
