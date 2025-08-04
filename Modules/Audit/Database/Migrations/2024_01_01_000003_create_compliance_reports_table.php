<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceReportsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Basic compliance reports storage
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            
            // Report metadata
            $table->string('report_type')->comment('Type of compliance report');
            $table->string('period')->comment('Report period');
            $table->text('description')->nullable()->comment('Report description');
            $table->string('status')->default('processing')->comment('Report status');
            
            // Report data
            $table->unsignedBigInteger('generated_by')->comment('User who generated the report');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Branch context');
            $table->string('file_path')->nullable()->comment('Exported file path');
            $table->unsignedBigInteger('file_size')->nullable()->comment('File size in bytes');
            $table->string('format')->default('pdf')->comment('Report format');
            $table->json('summary')->nullable()->comment('Report summary data');
            $table->json('metadata')->nullable()->comment('Report metadata');
            
            // Timestamps
            $table->timestamps();

            // Indexes for optimization
            $table->index('report_type'); // Report filtering
            $table->index('generated_by'); // User reports
            $table->index('branch_id'); // Branch reports
            $table->index('status'); // Status filtering
            $table->index('created_at'); // Date sorting
            
            // Composite indexes
            $table->index(['report_type', 'created_at']); // Report type timeline
            $table->index(['branch_id', 'created_at']); // Branch reports timeline
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compliance_reports');
    }
} 