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
        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->dateTime('pickup_time');
            $table->integer('number_of_passengers');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->boolean('self_drive')->default(false);
            $table->string('driver_license_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
    }
};
