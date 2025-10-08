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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode unik gudang
            $table->string('name');           // Nama gudang
            $table->string('type')->nullable(); // Jenis (misalnya: produksi, logistik)
            $table->string('pic')->nullable();  // Person In Charge
            $table->enum('capacity', ['kosong', 'Normal', 'Penuh'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->string('created_by')->nullable(); // siapa yang buat (opsional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
