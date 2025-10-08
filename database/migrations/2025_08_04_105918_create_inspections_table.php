<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // operator
            $table->string('shift')->nullable();
            $table->date('inspection_date')->nullable();

            $table->string('part_name');
            $table->string('supplier')->nullable();

            $table->enum('inspection_post', ['Incoming', 'Unloading', 'Buffing', 'Touch Up', 'Final']);
            $table->enum('check_method', ['100% (A)', 'Sampling (S)'])->nullable();
            $table->text('note')->nullable();

            $table->integer('total_check');
            $table->integer('total_ok')->default(0);
            $table->integer('total_ok_repair')->default(0);
            $table->integer('total_ng')->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};

