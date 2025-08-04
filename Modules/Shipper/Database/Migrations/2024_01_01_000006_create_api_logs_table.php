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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->string('endpoint', 500)->comment('API endpoint called');
            $table->string('method', 10)->comment('HTTP method (GET, POST, etc.)');
            $table->longText('request_data')->nullable()->comment('Request payload');
            $table->longText('response_data')->nullable()->comment('Response data');
            $table->integer('response_code')->nullable()->comment('HTTP response code');
            $table->integer('response_time_ms')->nullable()->comment('Response time in milliseconds');
            $table->text('error_message')->nullable()->comment('Error message if failed');
            $table->boolean('is_success')->default(false)->comment('Success status');
            $table->timestamp('logged_at')->comment('When the API call was logged');
            $table->timestamps();
            
            // Indexes
            $table->index('carrier_id');
            $table->index('is_success');
            $table->index('logged_at');
            $table->index('response_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}; 