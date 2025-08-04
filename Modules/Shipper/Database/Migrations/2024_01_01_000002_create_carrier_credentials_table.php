<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: JSON format API credentials storage per branch per carrier
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->json('credentials')->comment('Store API keys, secrets, tokens');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_tested_at')->nullable();
            $table->enum('test_result', ['success', 'failed'])->nullable();
            $table->text('test_error_message')->nullable();
            $table->timestamps();
            $table->foreignId('updated_by')->constrained('users');

            // Indexes for optimization
            $table->unique(['branch_id', 'carrier_id'], 'idx_carrier_credentials_unique');
            $table->index('branch_id', 'idx_carrier_credentials_branch');
            $table->index('carrier_id', 'idx_carrier_credentials_carrier');
            $table->index('is_active', 'idx_carrier_credentials_is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrier_credentials');
    }
} 