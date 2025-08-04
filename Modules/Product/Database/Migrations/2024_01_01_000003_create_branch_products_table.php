<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchProductsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Branch-specific product availability
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->decimal('branch_price', 8, 2)->nullable()->comment('Override global price');
            $table->timestamps();

            // Indexes for optimization
            $table->unique(['branch_id', 'product_id'], 'idx_branch_products_unique');
            $table->index('branch_id', 'idx_branch_products_branch');
            $table->index('product_id', 'idx_branch_products_product');
            $table->index('is_available', 'idx_branch_products_is_available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_products');
    }
} 