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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->year('year')->nullable();
            $table->enum('pricing_type', ['per_day','per_hour','per_km'])->default('per_day');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('city');
            $table->string('location');
            $table->boolean('is_private')->default(true);
            $table->boolean('is_taxi')->default(false);
            $table->string('type')->nullable(); // Sedan, SUV, Van...
            $table->enum('status',['pending','approved','rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
