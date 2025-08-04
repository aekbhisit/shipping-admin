<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Physical supplies catalog (boxes, tape, packaging materials)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('name')->comment('Product name');
            $table->text('description')->nullable();
            $table->string('sku', 100)->unique()->comment('Stock keeping unit');
            $table->string('image_path', 500)->nullable();
            $table->decimal('price', 8, 2)->default(0.00)->comment('Simple fixed prices');
            $table->string('unit', 50)->comment('piece, pack, roll, etc.');
            $table->json('dimensions')->nullable()->comment('L x W x H');
            $table->decimal('weight', 8, 3)->nullable()->comment('kg');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for optimization
            $table->index('category_id', 'idx_products_category');
            $table->index('sku', 'idx_products_sku');
            $table->index('name', 'idx_products_name');
            $table->index('is_active', 'idx_products_is_active');
            $table->index('sort_order', 'idx_products_sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
} 