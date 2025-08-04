<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiversTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Receiver information for shipments
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivers', function (Blueprint $table) {
            $table->id();
            $table->string('receiver_name')->comment('Name of the receiver');
            $table->string('receiver_phone', 20)->comment('Receiver contact phone');
            $table->string('receiver_email')->nullable();
            
            // Delivery address information
            $table->string('delivery_address_line_1')->comment('Primary delivery address');
            $table->string('delivery_address_line_2')->nullable()->comment('Secondary delivery address');
            $table->string('delivery_district', 100)->comment('Delivery district');
            $table->string('delivery_province', 100)->comment('Delivery province');
            $table->string('delivery_postal_code', 10)->comment('Delivery postal code');
            $table->string('delivery_country', 100)->default('Thailand');
            $table->text('delivery_instructions')->nullable()->comment('Special delivery instructions');
            
            $table->boolean('is_frequent')->default(false)->comment('Mark as frequent receiver');
            $table->timestamps();

            // Indexes for optimization
            $table->index('receiver_phone', 'idx_receivers_phone'); // For search
            $table->index('delivery_postal_code', 'idx_receivers_postal_code');
            $table->index('delivery_province', 'idx_receivers_province');
            $table->index('is_frequent', 'idx_receivers_is_frequent');
            
            // Composite indexes for location-based searches
            $table->index(['delivery_province', 'delivery_postal_code'], 'idx_receivers_location');
            $table->index(['receiver_phone', 'is_frequent'], 'idx_receivers_phone_frequent');
            
            // Full-text search on receiver name (if needed)
            $table->index('receiver_name', 'idx_receivers_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receivers');
    }
} 