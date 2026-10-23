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
        if (! Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('code');
                $table->string('name');
                $table->string('initial')->nullable();
                $table->string('category')->nullable();
                $table->date('join_date')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('coa_hutang')->nullable();
                $table->string('coa_retur')->nullable();
                $table->text('address')->nullable();
                $table->string('provinsi')->nullable();
                $table->string('city')->nullable();
                $table->string('kecamatan')->nullable();
                $table->string('kelurahan')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('telephone')->nullable();
                $table->string('mobile_phone')->nullable();
                $table->string('fax')->nullable();
                $table->string('email')->nullable();
                $table->integer('top')->nullable();
                $table->boolean('pkp')->default(0);
                $table->string('npwp_number')->nullable();
                $table->string('npwp_name')->nullable();
                $table->text('npwp_address')->nullable();
                $table->string('bank_type')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch')->nullable();
                $table->string('account_bank_name')->nullable();
                $table->string('account_bank_number')->nullable();
                $table->boolean('as_customer')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->unique(['code'], 'suppliers_code_unique');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
