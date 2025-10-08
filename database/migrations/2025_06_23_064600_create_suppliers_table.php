<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ATK00001SUPP
            $table->string('name');
            $table->string('initial');
            $table->string('category');

            $table->date('join_date')->nullable();
            $table->string('coa_hutang')->nullable();
            $table->string('coa_retur')->nullable();

            // Alamat
            $table->text('address')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('city')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('postal_code')->nullable();

            // Kontak
            $table->string('contact_person')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();

            // Akun & Pembayaran
            $table->integer('top')->nullable(); // Term of Payment
            $table->boolean('pkp')->default(false); // Taxable
            $table->string('npwp_number')->nullable();
            $table->string('npwp_name')->nullable();
            $table->text('npwp_address')->nullable();

            $table->string('bank_type')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_bank_name')->nullable();
            $table->string('account_bank_number')->nullable();

            $table->boolean('as_customer')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
