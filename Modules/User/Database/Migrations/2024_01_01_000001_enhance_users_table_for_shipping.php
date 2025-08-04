<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceUsersTableForShipping extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Enhance existing users table with shipping-specific fields
     * Enhancement Type: ADD_FIELDS (preserve existing functionality)
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Branch association (nullable for Company Admin)
            $table->foreignId('branch_id')->nullable()->constrained('branches')->comment('Branch assignment - null for Company Admin');
            
            // 3-tier user system
            $table->enum('user_type', ['company_admin', 'branch_admin', 'branch_staff'])
                  ->default('branch_staff')
                  ->comment('User role in shipping hierarchy');
            
            // Shipping-specific permissions
            $table->json('shipping_permissions')->nullable()->comment('Module-specific permissions storage');
            
            // Activity tracking
            $table->timestamp('last_branch_activity')->nullable()->comment('Last activity timestamp in branch context');
            
            // Soft delete implementation
            $table->boolean('is_active')->default(true)->comment('Soft delete flag - false = deactivated');
            $table->timestamp('deactivated_at')->nullable()->comment('When user was deactivated');
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->comment('User who performed deactivation');

            // Basic indexing per user preference
            $table->index('branch_id', 'idx_users_branch_id');
            $table->index('user_type', 'idx_users_user_type');
            $table->index('is_active', 'idx_users_is_active');
            
            // Composite index for common queries
            $table->index(['branch_id', 'user_type', 'is_active'], 'idx_users_branch_type_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_users_branch_id');
            $table->dropIndex('idx_users_user_type');
            $table->dropIndex('idx_users_is_active');
            $table->dropIndex('idx_users_branch_type_active');
            
            // Drop foreign key constraints
            $table->dropForeign(['deactivated_by']);
            $table->dropForeign(['branch_id']);
            
            // Drop columns
            $table->dropColumn([
                'branch_id',
                'user_type',
                'shipping_permissions',
                'last_branch_activity',
                'is_active',
                'deactivated_at',
                'deactivated_by'
            ]);
        });
    }
} 