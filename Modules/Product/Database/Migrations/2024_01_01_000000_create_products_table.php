<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->index();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('image', 500)->nullable();
            $table->string('slug', 255)->nullable()->unique();
            $table->boolean('status')->default(1)->index();
            $table->integer('sequence')->default(0);
            $table->timestamps();
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