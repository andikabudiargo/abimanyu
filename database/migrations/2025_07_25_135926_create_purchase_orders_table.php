<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->string('supplier_code');
            $table->integer('top')->nullable()->comment('Term of Payment in days');
            $table->boolean('pkp')->default(false)->comment('Taxable Person');
            $table->text('note')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->boolean('use_ppn')->default(true);
            $table->boolean('use_pph')->default(false);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('netto', 15, 2)->default(0);

            $table->timestamps();

            // Optional: foreign key ke supplier
            // $table->foreign('supplier_code')->references('code')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}

