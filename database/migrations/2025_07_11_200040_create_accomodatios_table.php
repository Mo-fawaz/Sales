<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
       Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); 
            $table->string('city');
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->integer('available_rooms');
            $table->float('rating')->default(0);
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('accommodatios');
    }
};
