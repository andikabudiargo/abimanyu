<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferOutTable extends Migration
{
    public function up()
    {
        Schema::create('transfer_out', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode Transfer Out
            $table->string('reference_number')->nullable(); // Transfer In Code yang jadi referensi
            $table->date('date');
            $table->string('transfer_type'); // Transfer Loading, Mutasi, Trial, dll
            $table->text('note')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_out');
    }
}
