<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('purchase_request_id'); // refer ke PR
            $table->string('article_code');
            $table->string('article_description')->nullable();
            $table->decimal('qty', 15, 2);
            $table->string('uom')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);

            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            // Jika ada tabel purchase_requests
            // $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}

