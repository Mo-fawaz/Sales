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
        Schema::create('owner', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->references('id')->on("users")->onDelete('cascade');; 
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('national_id')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner');
    }
};
