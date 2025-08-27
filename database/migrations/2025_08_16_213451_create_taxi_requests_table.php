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
        Schema::create('taxi_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستأجر
            $table->foreignId('car_id')->nullable()->constrained()->onDelete('set null'); // السيارة المقبولة
            $table->string('status')->default('pending'); // pending - accepted - rejected - cancelled
            $table->string('pickup_location');
            $table->string('dropoff_location')->nullable();
            $table->integer('passengers')->default(1);
            $table->string('car_type')->nullable(); // sedan, suv ...
            $table->string('rejection_reason')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxi_requests');
    }
};
