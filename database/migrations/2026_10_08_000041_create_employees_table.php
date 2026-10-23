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
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->charset('latin1');
                $table->collation('latin1_swedish_ci');

                $table->integer('id')->autoIncrement();
                $table->string('employee_type', 50)->nullable();
                $table->integer('nik');
                $table->string('name');
                $table->enum('gender', ['Laki-Laki', 'Perempuan'])->nullable();
                $table->date('birth_date')->nullable();
                $table->string('birth_place')->nullable();
                $table->string('religion', 100)->nullable();
                $table->string('nationality', 100)->nullable();
                $table->boolean('marriage_status')->default(0);
                $table->boolean('spouse_work')->default(0);
                $table->integer('dependents')->nullable();
                $table->string('last_education')->nullable();
                $table->integer('instansi')->nullable();
                $table->date('join_date')->nullable();
                $table->integer('position_id')->nullable();
                $table->text('address')->nullable();
                $table->string('provinsi', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('kecamatan', 100)->nullable();
                $table->string('kelurahan', 100)->nullable();
                $table->string('phone', 100)->nullable();
                $table->string('phone_emergency_1', 100)->nullable();
                $table->string('phone_emergency_2', 100)->nullable();
                $table->string('email')->nullable();
                $table->string('ktp_number', 100)->nullable();
                $table->string('kk_number', 100)->nullable();
                $table->string('npwp_number', 100)->nullable();
                $table->string('bpjs_tk', 100)->nullable();
                $table->string('bpjs_ks', 100)->nullable();
                $table->string('bank_number', 100)->nullable();
                $table->string('bank_account_name', 100)->nullable();
                $table->text('photo_profile')->nullable();
                $table->text('cv')->nullable();
                $table->text('ktp_file')->nullable();
                $table->text('kk_file')->nullable();
                $table->text('skck_file')->nullable();
                $table->text('ijazah')->nullable();
                $table->text('mcu')->nullable();
                $table->boolean('eat')->default(1);
                $table->string('status', 50)->default('Active');
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->useCurrent();

                $table->primary(['id']);
                $table->unique(['nik'], 'nik');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
