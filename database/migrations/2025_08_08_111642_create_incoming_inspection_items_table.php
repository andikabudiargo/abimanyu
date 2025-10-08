<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_inspection_id')->constrained('incoming_inspections')->onDelete('cascade');
            $table->unsignedBigInteger('inspection_id'); // relasi ke tabel inspections
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_inspection_items');
    }
};

