<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat struktur tabel sesuai skema dump produksi (abimany3_abimanyu_db).
     */
    public function up(): void
    {
        if (! Schema::hasTable('booking_rooms')) {
            Schema::create('booking_rooms', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->date('booking_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('purpose', 100);
                $table->string('description');
                $table->string('status', 100)->default('Waiting Approval');
                $table->integer('room_id');
                $table->integer('created_by');
                $table->dateTime('created_at')->useCurrent();
                $table->integer('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->integer('cancel_by')->nullable();
                $table->dateTime('cancel_at')->nullable();
                $table->text('cancel_reason')->nullable();
                $table->dateTime('updated_at')->useCurrent();

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
