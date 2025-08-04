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
        Schema::create('shipping_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('cascade');
            $table->string('tracking_number', 255)->comment('Carrier tracking number');
            $table->string('label_format', 50)->comment('Label format (PDF, PNG, etc.)');
            $table->string('label_path', 500)->comment('File path to stored label');
            $table->longText('label_data')->nullable()->comment('Base64 or file content');
            $table->timestamp('generated_at')->comment('When label was generated');
            $table->timestamps();
            
            // Indexes
            $table->index('shipment_id');
            $table->index('carrier_id');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_labels');
    }
}; 