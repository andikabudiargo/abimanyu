<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Barcode atau kode artikel
            $table->string('name');           // Nama artikel
            $table->string('type');           // Tipe (contoh: Paint, Solvent, dll)
            $table->string('unit');           // UOM - Unit of Measurement (kg, liter, pcs, dll)
            $table->string('customer')->nullable(); // Customer / Supplier
            $table->integer('safety_stock')->default(0); // Safety stock
            $table->integer('min_package')->default(1);  // Minimum package quantity
            $table->string('qr_code_path')->nullable();  // Path ke file gambar QR Code (jika disimpan)
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status aktif/non
            $table->text('note')->nullable();       // Catatan tambahan
            $table->timestamps();                   // created_at dan updated_at
            $table->unsignedBigInteger('created_by')->nullable(); // ID user pembuat

            // Opsional: foreign key ke tabel users jika ada
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

