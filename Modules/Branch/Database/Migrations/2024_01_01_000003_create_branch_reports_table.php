<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchReportsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Basic performance analytics per branch (shipment count and revenue)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('report_date')->comment('Date for the report data');
            $table->integer('shipment_count')->default(0)->comment('Basic shipment count');
            $table->decimal('total_revenue', 12, 2)->default(0.00)->comment('Total revenue for the day');
            $table->decimal('total_markup', 12, 2)->default(0.00)->comment('Total markup earned');
            $table->json('report_data')->nullable()->comment('Additional metrics and data');
            $table->timestamps();

            // Indexes for optimization
            $table->unique(['branch_id', 'report_date'], 'idx_branch_reports_unique'); // One report per branch per date
            $table->index('branch_id', 'idx_branch_reports_branch');
            $table->index('report_date', 'idx_branch_reports_date');
            
            // Composite index for range queries
            $table->index(['branch_id', 'report_date'], 'idx_branch_reports_branch_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_reports');
    }
} 