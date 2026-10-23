<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Buat struktur tabel sesuai skema dump produksi (abimany3_abimanyu_db).
     *
     * Mode SQL diturunkan sementara agar default tanggal-nol (0000-00-00) dari dump
     * diterima MySQL; mode sesi sebelumnya dipulihkan setelah tabel dibuat.
     */
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            $previousSqlMode = DB::selectOne('SELECT @@SESSION.sql_mode AS m')->m;
            DB::statement("SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->integer('user_id');
                $table->longText('subscription');
                $table->string('ip_address', 500);
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->default('0000-00-00 00:00:00');

                $table->primary(['id']);
            });
            DB::statement("SET SESSION sql_mode = '" . $previousSqlMode . "'");
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
