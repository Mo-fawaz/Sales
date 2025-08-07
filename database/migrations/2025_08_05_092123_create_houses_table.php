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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب العقار
            $table->string('title'); // اسم البيت
            $table->text('description')->nullable(); // وصف
            $table->string('location'); // الموقع
            $table->decimal('price_per_night', 8, 2); // السعر لليلة
            $table->integer('max_guests')->default(1); // عدد الضيوف المسموح
            $table->json('amenities')->nullable(); // الخدمات (تلفزيون، واي فاي...)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
