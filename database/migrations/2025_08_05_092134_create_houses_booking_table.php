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
        Schema::create('houses_booking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on("users")->onDelete('cascade');
            $table->unsignedBigInteger('house_id');
            $table->foreign('house_id')->references('id')->on("houses")->onDelete('cascade');
            $table->date('start_date'); // تاريخ بدء الحجز
            $table->date('end_date');   // تاريخ نهاية الحجز
            $table->decimal('total_price', 8, 2); // السعر الكلي
            $table->string('status')->default('pending'); // حالة الحجز: pending, confirmed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses_booking');
    }
};
