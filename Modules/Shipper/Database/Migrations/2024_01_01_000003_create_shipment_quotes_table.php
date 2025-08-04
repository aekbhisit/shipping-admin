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
        Schema::create('shipment_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->decimal('original_price', 10, 2)->comment('Original price from carrier API');
            $table->decimal('markup_percentage', 5, 2)->default(0.00)->comment('Markup percentage applied');
            $table->decimal('final_price', 10, 2)->comment('Final price after markup');
            $table->string('service_type', 255)->comment('Carrier service type');
            $table->integer('estimated_delivery_days')->nullable()->comment('Estimated delivery days');
            $table->json('quote_data')->nullable()->comment('Full API response data');
            $table->boolean('is_selected')->default(false)->comment('Selected quote indicator');
            $table->timestamp('quoted_at')->comment('When quote was generated');
            $table->timestamp('expires_at')->nullable()->comment('Quote expiration time');
            $table->timestamps();
            
            // Indexes
            $table->index('shipment_id');
            $table->index('carrier_id');
            $table->index('is_selected');
            $table->index('quoted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_quotes');
    }
}; 