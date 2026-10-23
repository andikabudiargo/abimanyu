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
        if (! Schema::hasTable('cancel_booking_rooms')) {
            Schema::create('cancel_booking_rooms', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('booking_id');
                $table->integer('user_id');
                $table->integer('room_id');
                $table->date('booking_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('purpose', 100);
                $table->text('reason')->nullable();
                $table->dateTime('created_at')->useCurrent();
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
        Schema::dropIfExists('cancel_booking_rooms');
    }
};
