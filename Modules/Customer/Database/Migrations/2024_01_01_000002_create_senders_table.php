<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendersTable extends Migration
{
    /**
     * Run the migrations.
     * Purpose: Sender details with multiple addresses (unlimited per user preference)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('sender_name')->comment('Name of the sender');
            $table->string('sender_phone', 20)->comment('Sender contact phone');
            $table->string('sender_email')->nullable();
            $table->foreignId('default_address_id')->nullable()->comment('FK to addresses table');
            $table->json('preferences')->nullable()->comment('Sender-specific preferences');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for optimization
            $table->index('customer_id', 'idx_senders_customer');
            $table->index('sender_phone', 'idx_senders_phone'); // For search
            $table->index('is_active', 'idx_senders_is_active');
            $table->index('default_address_id', 'idx_senders_default_address');
            
            // Composite index for active senders by customer
            $table->index(['customer_id', 'is_active'], 'idx_senders_customer_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('senders');
    }
} 