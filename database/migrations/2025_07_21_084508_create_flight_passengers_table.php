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
        Schema::create('flight_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('flight_bookings')->onDelete('cascade');
            $table->string('traveler_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');

            $table->date('date_of_birth');
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('email');
            $table->string('phone');
            $table->string('passport_number');
            $table->string('nationality');
            $table->date('passport_expiry');
            $table->string('amadeus_id')->nullable(); // same TravelerId in amadeus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_passengers');
    }
};
