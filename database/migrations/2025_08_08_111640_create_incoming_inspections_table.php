<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('incoming_number')->unique();
            $table->string('supplier_code');
            $table->string('article_code');
            $table->string('period', 7); // format YYYY-MM
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_inspections');
    }
};
