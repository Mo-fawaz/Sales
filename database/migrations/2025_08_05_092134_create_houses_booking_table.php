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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الزبون يلي حجز
            $table->foreignId('house_id')->constrained()->onDelete('cascade'); // البيت المحجوز
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
