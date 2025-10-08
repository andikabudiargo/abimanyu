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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // kode ruangan, auto-generate
            $table->string('name');             // nama ruangan
            $table->integer('capacity')->nullable(); // kapasitas ruangan
            $table->json('equipment')->nullable();   // multiple equipment
            $table->string('location');         // lokasi (single)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
