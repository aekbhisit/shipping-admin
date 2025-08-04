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
        Schema::create('tracking_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->string('tracking_number', 255)->comment('Carrier tracking number');
            $table->string('status', 255)->comment('Current tracking status');
            $table->string('location', 255)->nullable()->comment('Current location');
            $table->text('description')->nullable()->comment('Status description');
            $table->json('tracking_data')->nullable()->comment('Full API response data');
            $table->timestamp('updated_at_carrier')->comment('Timestamp from carrier');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('User who updated manually');
            $table->timestamps();
            
            // Indexes
            $table->index('shipment_id');
            $table->index('carrier_id');
            $table->index('tracking_number');
            $table->index('status');
            $table->index('updated_at_carrier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_updates');
    }
}; 