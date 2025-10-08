<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('suppliers', function (Blueprint $table) {
        $table->enum('status', ['active', 'inactive'])->default('active')->after('join_date');
    });
}

public function down()
{
    Schema::table('suppliers', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
