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
        if (! Schema::hasTable('it_assets')) {
            Schema::create('it_assets', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('asset_number', 100)->nullable();
                $table->string('asset_name');
                $table->string('asset_type', 100);
                $table->string('acquistion_type', 50)->nullable();
                $table->string('supplier_id')->nullable();
                $table->date('purchase_date')->nullable();
                $table->integer('warranty')->nullable();
                $table->string('assignment_type', 100);
                $table->string('location')->nullable();
                $table->string('location_update')->nullable();
                $table->integer('assigned_to')->nullable();
                $table->string('pic', 200)->nullable();
                $table->string('conditions', 100)->nullable();
                $table->string('status', 100)->default('Available');
                $table->text('photo')->nullable();
                $table->text('note')->nullable();
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
        Schema::dropIfExists('it_assets');
    }
};
