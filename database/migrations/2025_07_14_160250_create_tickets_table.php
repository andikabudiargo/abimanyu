<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Nomor Tiket Otomatis
            $table->string('ticket_number')->unique();

            // Kategori: New Program, New Feature, Change Request
            $table->string('category');

            // Judul & Deskripsi
            $table->string('title');
            $table->text('description')->nullable();

            // Path atau nama file lampiran
            $table->string('attachment')->nullable();

            // Status default dan prioritas (bisa ditambahkan nanti)
            $table->string('status')->default('Pending'); // Open, In Progress, Resolved, Closed
            $table->string('priority')->nullable(); // Optional (Low, Medium, High, Urgent)

            // User yang mengirim tiket (jika sistem punya auth)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('approved_by')->nullable();
$table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

$table->timestamp('approved_at')->nullable();
$table->text('rejected_reason')->nullable(); // Untuk penolakan tiket

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
}

