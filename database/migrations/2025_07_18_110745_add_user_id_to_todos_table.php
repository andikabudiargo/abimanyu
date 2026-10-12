<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tidak ada aksi: kolom `user_id` sudah termasuk dalam migration `create_todos_table`
     * (baseline skema dump produksi), sehingga tidak perlu ditambahkan lagi.
     */
    public function up(): void
    {
    }

    /**
     * Tidak ada aksi: kolom dimiliki oleh migration `create_todos_table`, bukan migration ini.
     */
    public function down(): void
    {
    }
};
