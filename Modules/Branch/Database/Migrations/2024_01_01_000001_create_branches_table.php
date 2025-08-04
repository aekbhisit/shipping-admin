<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Branch details and configuration with isolation support
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Branch name');
            $table->string('code', 50)->unique()->comment('Branch identifier code');
            $table->text('address')->comment('Branch address');
            $table->string('phone', 20)->comment('Branch contact phone');
            $table->string('email')->comment('Branch contact email');
            $table->string('contact_person')->comment('Branch contact person');
            $table->boolean('is_active')->default(true)->comment('Simple active/inactive status');
            $table->json('operating_hours')->nullable()->comment('Store operating hours');
            $table->json('settings')->nullable()->comment('Branch-specific settings');
            $table->timestamps();
            
            // Track who created the branch
            $table->foreignId('created_by')->constrained('users')->comment('User who created the branch');

            // Indexes for basic optimization per user preference
            $table->index('code', 'idx_branches_code'); // Unique branch code
            $table->index('is_active', 'idx_branches_is_active'); // Basic indexing
            $table->index('name', 'idx_branches_name'); // For search functionality
            $table->index('created_by', 'idx_branches_created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
} 