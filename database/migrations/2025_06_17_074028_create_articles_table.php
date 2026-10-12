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
        if (! Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                $table->charset('utf8mb4');
                $table->collation('utf8mb4_unicode_ci');

                $table->id();
                $table->string('supplier_code', 50)->nullable();
                $table->string('supplier_name')->nullable();
                $table->string('article_code');
                $table->string('description');
                $table->string('color')->nullable();
                $table->string('model')->nullable();
                $table->string('unit', 50);
                $table->decimal('conversion_value', 50, 2)->nullable();
                $table->string('article_type');
                $table->integer('safety_stock')->nullable();
                $table->integer('maximum_stock')->default(0);
                $table->decimal('min_package', 15, 2)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('note')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();

                $table->unique(['article_code'], 'articles_code_unique');
                $table->index(['supplier_code'], 'idx_articles_supplier_code');
            });
        }
    }

    /**
     * Hapus tabel yang dibuat oleh migration ini.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
