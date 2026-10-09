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
        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('category', 100);
                $table->string('title');
                $table->text('description');
                $table->date('display_start');
                $table->date('display_end');
                $table->integer('created_by')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');

                $table->primary(['id']);
            });
        }

        if (! Schema::hasTable('announcement_attachments')) {
            Schema::create('announcement_attachments', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('announcement_id');
                $table->string('path');
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->dateTime('updated_at');

                $table->primary(['id']);
            });
        }

        if (! Schema::hasTable('announcement_recipient')) {
            Schema::create('announcement_recipient', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('announcement_id');
                $table->integer('user_id');

                $table->primary(['id']);
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_recipient');
        Schema::dropIfExists('announcement_attachments');
        Schema::dropIfExists('announcements');
    }
};
