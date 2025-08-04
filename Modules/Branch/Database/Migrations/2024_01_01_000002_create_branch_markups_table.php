<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchMarkupsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Simple markup rules per branch per carrier (fixed percentage)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_markups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->decimal('markup_percentage', 5, 2)->default(0.00)->comment('Fixed percentage markup');
            $table->decimal('min_markup_amount', 8, 2)->default(0.00)->comment('Minimum markup amount');
            $table->decimal('max_markup_percentage', 5, 2)->default(100.00)->comment('Maximum markup percentage');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Track who updated the markup
            $table->foreignId('updated_by')->constrained('users')->comment('User who last updated markup');

            // Indexes for optimization
            $table->unique(['branch_id', 'carrier_id'], 'idx_branch_markups_unique'); // One markup per branch per carrier
            $table->index('branch_id', 'idx_branch_markups_branch'); // Basic indexing
            $table->index('carrier_id', 'idx_branch_markups_carrier');
            $table->index('is_active', 'idx_branch_markups_is_active');
            $table->index('updated_by', 'idx_branch_markups_updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_markups');
    }
} 