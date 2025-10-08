<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferOutItemsTable extends Migration
{
    public function up()
    {
        Schema::create('transfer_out_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_out_id'); // Relasi ke Transfer Out
            $table->unsignedBigInteger('transfer_in_item_id')->nullable(); // Relasi ke transfer_in_items
            $table->string('transfer_in_code')->nullable(); // Simpan kode Transfer In (untuk history)
            
            $table->string('article_code');
            $table->string('description');
            $table->integer('qty')->default(0);
            $table->string('uom')->nullable();
            $table->integer('min_package')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('from_location')->nullable();
            $table->string('destination')->nullable();
            
            $table->timestamps();

            // Foreign Key
            $table->foreign('transfer_out_id')->references('id')->on('transfer_out')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_out_items');
    }
}
