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
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // e.g., 'suite', 'deluxe', 'standard', 'villa'
            $table->integer('capacity'); // Number of guests
            $table->decimal('price_per_night', 10, 2);
            $table->boolean('available')->default(true);
            $table->string('image_url')->nullable();
            $table->json('amenities')->nullable(); // JSON array of amenities
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
