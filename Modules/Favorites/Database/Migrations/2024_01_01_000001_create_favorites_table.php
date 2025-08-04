<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('favorable_type'); // The model class (e.g., Shipment, Customer, Product)
            $table->unsignedBigInteger('favorable_id'); // The model ID
            $table->string('favorite_type')->default('like'); // like, bookmark, star, etc.
            $table->text('notes')->nullable(); // Optional notes about the favorite
            $table->timestamps();

            // Ensure a user can only favorite an item once
            $table->unique(['user_id', 'favorable_type', 'favorable_id', 'favorite_type'], 'unique_user_favorite');
            
            // Index for efficient queries
            $table->index(['favorable_type', 'favorable_id']);
            $table->index(['user_id', 'favorite_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
}; 