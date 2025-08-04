<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Top-level customer entity with UUID uniqueness and cross-branch sharing
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique()->comment('UUID for customer uniqueness');
            $table->string('customer_code', 50)->unique()->comment('Generated from UUID');
            $table->enum('customer_type', ['individual', 'business'])->comment('Customer type');
            $table->string('company_name')->nullable()->comment('For business customers');
            $table->string('individual_name')->nullable()->comment('For individual customers');
            $table->string('tax_id', 50)->nullable();
            $table->string('phone', 20)->comment('Primary contact phone');
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Track creation origin for analytics (but allow cross-branch access)
            $table->foreignId('created_by_branch')->constrained('branches')->comment('Branch that created customer');
            $table->foreignId('created_by_user')->constrained('users')->comment('User that created customer');

            // Indexes for optimization and search
            $table->index('uuid', 'idx_customers_uuid');
            $table->index('customer_code', 'idx_customers_code');
            $table->index('phone', 'idx_customers_phone'); // For fuzzy matching
            $table->index('email', 'idx_customers_email'); // For fuzzy matching
            $table->index('customer_type', 'idx_customers_type');
            $table->index('is_active', 'idx_customers_is_active');
            $table->index('created_by_branch', 'idx_customers_created_branch');
            
            // Composite index for search optimization
            $table->index(['is_active', 'customer_type'], 'idx_customers_active_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
} 