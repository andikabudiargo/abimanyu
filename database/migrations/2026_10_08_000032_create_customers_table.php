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
        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_0900_ai_ci');

                $table->integer('id')->autoIncrement();
                $table->string('code', 50)->nullable()->collation('utf8mb4_unicode_ci');
                $table->string('name');
                $table->string('initial', 50);
                $table->string('contact_person', 100)->nullable();
                $table->string('telephone', 50)->nullable();
                $table->string('handphone', 50)->nullable();
                $table->string('fax', 100)->nullable();
                $table->text('alamat_tagih')->nullable();
                $table->text('alamat_kirim_1')->nullable();
                $table->text('alamat_kirim_2')->nullable();
                $table->string('npwp', 100)->nullable();
                $table->string('nppkp')->nullable();
                $table->text('alamat_npwp')->nullable();
                $table->enum('epte', ['Yes', 'No'])->nullable();
                $table->string('coa_piutang', 100)->nullable();
                $table->string('coa_penjualan', 100)->nullable();
                $table->integer('term_of_payment')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
