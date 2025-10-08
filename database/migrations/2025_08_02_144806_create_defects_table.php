<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->string('defect');
            $table->boolean('raw_material')->default(false);
            $table->string('description')->nullable();
            $table->enum('inspection_post', ['Incoming', 'Unloading', 'Buffing', 'Touch Up', 'Final'])->nullable();
            $table->enum('category', ['NG', 'NC', 'Both'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('defects');
    }
}

