<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Login, logout, and significant actions tracking
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            
            // User and branch context
            $table->unsignedBigInteger('user_id')->nullable()->comment('User performing the activity');
            $table->string('user_name')->nullable()->comment('User name for display');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('Branch context');
            
            // Activity tracking
            $table->string('action')->comment('Type of user activity');
            $table->string('module')->nullable()->comment('Module where activity occurred');
            $table->text('description')->comment('Detailed activity description');
            
            // Context information
            $table->string('ip_address')->nullable()->comment('User IP address');
            $table->text('user_agent')->nullable()->comment('User browser/agent');
            $table->json('details')->nullable()->comment('Extra context data');
            
            // Timestamps
            $table->timestamps();

            // Indexes for optimization
            $table->index('user_id'); // User activity lookup
            $table->index('branch_id'); // Branch activity
            $table->index('action'); // Activity filtering
            $table->index('module'); // Module filtering
            $table->index('created_at'); // Date sorting
            
            // Composite indexes for common queries
            $table->index(['user_id', 'action']); // User specific activity
            $table->index(['branch_id', 'created_at']); // Branch activity timeline
            $table->index(['action', 'created_at']); // Activity type timeline
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activity_logs');
    }
} 