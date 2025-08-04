<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Separate addresses table with foreign keys and favorites support
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('senders')->onDelete('cascade');
            $table->enum('address_type', ['pickup', 'delivery'])->default('pickup');
            $table->string('address_line_1')->comment('Primary address line');
            $table->string('address_line_2')->nullable()->comment('Secondary address line');
            $table->string('district', 100)->comment('District/Sub-district');
            $table->string('province', 100)->comment('Province/State');
            $table->string('postal_code', 10)->comment('Postal/ZIP code');
            $table->string('country', 100)->default('Thailand');
            $table->decimal('latitude', 10, 8)->nullable()->comment('GPS latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('GPS longitude');
            $table->boolean('is_default')->default(false)->comment('Default address for sender');
            $table->boolean('is_favorite')->default(false)->comment('Address book favorites');
            $table->timestamps();

            // Indexes for optimization
            $table->index('sender_id', 'idx_addresses_sender');
            $table->index('postal_code', 'idx_addresses_postal_code');
            $table->index('province', 'idx_addresses_province');
            $table->index('is_default', 'idx_addresses_is_default');
            $table->index('is_favorite', 'idx_addresses_is_favorite');
            $table->index('address_type', 'idx_addresses_type');
            
            // Composite indexes for queries
            $table->index(['sender_id', 'is_default'], 'idx_addresses_sender_default');
            $table->index(['sender_id', 'is_favorite'], 'idx_addresses_sender_favorite');
            $table->index(['province', 'postal_code'], 'idx_addresses_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
} 