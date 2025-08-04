<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Carrier/shipper information and configuration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Thailand Post, J&T Express, Flash Express');
            $table->string('code', 50)->unique()->comment('TP, JT, FLASH');
            $table->string('api_base_url', 500);
            $table->string('api_version', 20)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('supported_services')->nullable()->comment('Array of service types');
            $table->string('api_documentation_url', 500)->nullable();
            $table->timestamps();

            // Indexes for optimization
            $table->index('code', 'idx_carriers_code');
            $table->index('is_active', 'idx_carriers_is_active');
            $table->index('name', 'idx_carriers_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carriers');
    }
} 