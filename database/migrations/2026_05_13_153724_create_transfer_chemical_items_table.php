<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_chemical_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_chemical_id')
                  ->constrained('transfer_chemicals')
                  ->cascadeOnDelete();
            $table->foreignId('article_code')
                  ->constrained('articles')
                  ->restrictOnDelete();
            $table->enum('condition', ['Utuh', 'Tidak Utuh']);
            $table->decimal('qty', 10, 2);
            $table->string('unit', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_chemical_items');
    }
};