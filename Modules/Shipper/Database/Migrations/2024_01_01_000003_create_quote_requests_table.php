<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Track quote requests for debugging and analytics
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->json('request_data')->comment('Package details sent to API');
            $table->json('response_data')->nullable()->comment('Full API response');
            $table->decimal('quote_price', 10, 2)->nullable()->comment('Parsed price');
            $table->string('service_type', 100)->nullable()->comment('Service selected');
            $table->boolean('is_successful')->default(false);
            $table->text('error_message')->nullable();
            $table->integer('processing_time_ms')->nullable()->comment('Response time');
            $table->timestamp('requested_at');
            $table->foreignId('requested_by')->constrained('users');

            // Indexes for optimization
            $table->index('branch_id', 'idx_quote_requests_branch');
            $table->index('carrier_id', 'idx_quote_requests_carrier');
            $table->index('requested_at', 'idx_quote_requests_requested_at');
            $table->index('is_successful', 'idx_quote_requests_is_successful');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_requests');
    }
} 