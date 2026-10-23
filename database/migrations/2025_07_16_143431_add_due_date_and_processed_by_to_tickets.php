<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tidak ada aksi: kolom `due_date`, `processed_by`, `processed_at` sudah termasuk dalam migration `create_tickets_table`
     * (baseline skema dump produksi), sehingga tidak perlu ditambahkan lagi.
     */
    public function up(): void
    {
    }

    /**
     * Tidak ada aksi: kolom dimiliki oleh migration `create_tickets_table`, bukan migration ini.
     */
    public function down(): void
    {
    }
};
