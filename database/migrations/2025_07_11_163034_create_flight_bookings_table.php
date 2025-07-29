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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();         // معرف الحجز من النظام الخارجي (مثلاً Amadeus)
            $table->string('reference')->unique();        // رمز الحجز (PNR)
            $table->string('email')->nullable();          // إيميل المسافر أو صاحب الحجز
            $table->string('status')->default('pending'); // حالة الحجز (pending, paid, confirmed, canceled)
            $table->decimal('total_price', 10, 2);        // السعر الكلي
            $table->string('currency', 3)->default('EUR'); // العملة
            $table->string('origin', 3);                   // مطار الانطلاق (رمز IATA)
            $table->string('destination', 3);              // مطار الوصول (رمز IATA)
            $table->dateTime('departure_time');            // وقت الإقلاع
            $table->dateTime('arrival_time');              // وقت الوصول
            $table->string('airline', 10);                 // رمز الخطوط الجوية
            $table->unsignedTinyInteger('segments_count'); // عدد المقاطع
            $table->json('data')->nullable();              // بيانات الحجز كاملة (raw JSON)
            $table->string('ticket_number')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
