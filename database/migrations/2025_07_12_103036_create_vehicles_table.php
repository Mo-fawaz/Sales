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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['taxi', 'private', 'van', 'luxury']);
            $table->integer('seats');
            $table->decimal('price_per_trip', 8, 2)->nullable();
            $table->string('location');
            $table->boolean('self_drive')->default(false);
            $table->dateTime('available_from');
            $table->dateTime('available_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
