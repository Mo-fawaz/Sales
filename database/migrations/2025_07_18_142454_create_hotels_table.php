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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->decimal('price_per_night', 10, 2)->nullable()->default(123.45);
            $table->json('images')->nullable();
            $table->boolean('is_avalible')->default(true);
            $table->unsignedTinyInteger('stars')->default(3); // From 1 to 5
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
