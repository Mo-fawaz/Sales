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
        Schema::create('flight_searches', function (Blueprint $table) {
            $table->id();
            $table->string('origin', 5);
            $table->string('destination', 5);
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->string('travel_class', 20)->default('economy');
            $table->string('search_id')->nullable(); // من API إذا رجع
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_search_results');
    }
};
