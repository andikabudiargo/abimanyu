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
       Schema::create('transfer_in_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_in_id');
            $table->string('transaction_number');
            $table->string('article_code');
            $table->string('description')->nullable();
            $table->integer('qty');
            $table->string('uom')->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamps();

            $table->foreign('transfer_in_id')->references('id')->on('transfer_in')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_transfer_in_item');
    }
};
