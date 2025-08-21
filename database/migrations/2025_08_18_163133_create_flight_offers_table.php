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
        Schema::create('flight_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flight_search_id')
                ->constrained('flight_searches')
                ->onDelete('cascade');

            $table->string('offer_id', 100);
            $table->string('carrier_code', 10);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3);
            $table->string('fare_basis')->nullable();           // FJR3R1FO
            $table->string('branded_fare')->nullable();         // FELITE
            $table->string('branded_fare_label')->nullable();   // FIRST ELITE
            $table->unsignedTinyInteger('cabin_bags')->nullable(); // عدد حقائب اليد
            $table->decimal('checked_bags_weight', 5, 2)->nullable(); // الوزن
            $table->string('checked_bags_unit', 5)->nullable();       // الوحدة KG
            $table->dateTime('last_ticketing_date')->nullable();


            $table->string('departure_airport', 5);
            $table->string('arrival_airport', 5);
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->string('duration', 20);
            $table->unsignedTinyInteger('stops')->default(0);
            $table->json('all_details')->nullable();


            $table->timestamps();

            $table->index('offer_id', 'idx_offer_id'); // للبحث السريع عن عرض محدد
            $table->index('flight_search_id', 'idx_search_id'); // للبحث حسب نتيجة البحث
            $table->index(['departure_airport', 'arrival_airport'], 'idx_route'); // للبحث حسب route
            $table->index('departure_time', 'idx_departure_time'); // للاستعلامات الزمنية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_offers');
    }
};
