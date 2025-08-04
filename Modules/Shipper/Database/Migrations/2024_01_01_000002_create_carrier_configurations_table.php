<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade')->comment('null = global config');
            $table->string('api_username', 255)->nullable()->comment('API username credential');
            $table->string('api_password', 255)->nullable()->comment('API password credential');
            $table->string('api_key', 255)->nullable()->comment('API key credential');
            $table->string('api_secret', 255)->nullable()->comment('API secret credential');
            $table->boolean('is_active')->default(true)->comment('Configuration active status');
            $table->foreignId('created_by')->constrained('users')->comment('User who created this configuration');
            $table->timestamps();
            
            // Indexes
            $table->unique(['carrier_id', 'branch_id'], 'unique_carrier_branch_config');
            $table->index('carrier_id');
            $table->index('branch_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrier_configurations');
    }
}; 