<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tidak ada aksi: kolom `status` sudah termasuk dalam migration `create_suppliers_table`
     * (baseline skema dump produksi), sehingga tidak perlu ditambahkan lagi.
     */
    public function up(): void
    {
    }

    /**
     * Tidak ada aksi: kolom dimiliki oleh migration `create_suppliers_table`, bukan migration ini.
     */
    public function down(): void
    {
    }
};
