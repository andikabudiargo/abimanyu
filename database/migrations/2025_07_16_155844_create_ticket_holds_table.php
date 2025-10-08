<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('ticket_holds', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
        $table->string('reason');
        $table->text('description')->nullable(); // Untuk custom reason
        $table->timestamp('start_at')->nullable();
        $table->timestamp('end_at')->nullable();
        $table->foreignId('created_by')->constrained('users'); // siapa yang melakukan hold
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_holds');
    }
};
