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
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('name');
                $table->string('username');
                $table->unsignedBigInteger('role_id')->nullable();
                $table->string('employee_id')->nullable();
                $table->unsignedBigInteger('department_id')->nullable();
                $table->string('position')->nullable();
                $table->string('phone')->nullable();
                $table->string('avatar')->nullable();
                $table->string('email')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->boolean('status')->default(1);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->dateTime('last_login')->nullable();
                $table->string('last_ip')->nullable();
                $table->longText('subscription')->nullable();

                $table->index(['department_id'], 'department_id');
                $table->index(['role_id'], 'role_id');
                $table->unique(['username'], 'username');
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->string('email');
                $table->string('token');
                $table->timestamp('created_at')->nullable();

                $table->primary(['email']);
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->string('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity');

                $table->primary(['id']);
                $table->index(['last_activity'], 'sessions_last_activity_index');
                $table->index(['user_id'], 'sessions_user_id_index');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
