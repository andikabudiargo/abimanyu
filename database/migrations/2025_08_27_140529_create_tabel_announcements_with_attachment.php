<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsWithAttachmentsAndReceipents extends Migration
{
    public function up()
    {
        // Tabel utama announcement
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['event', 'himbauan', 'warning', 'other'])->default('other');
            $table->date('display_start');
            $table->date('display_end');
            $table->timestamps();
        });

        // Tabel attachment (bisa multiple)
        Schema::create('announcement_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
            $table->string('filename');        // nama file di server
            $table->timestamps();
        });

        // Pivot table untuk recipients
        Schema::create('announcement_recipient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcement_recipient');
        Schema::dropIfExists('announcement_attachments');
        Schema::dropIfExists('announcements');
    }
};
