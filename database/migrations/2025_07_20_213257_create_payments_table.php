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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_booking_id')->constrained()->onDelete('cascade');
            $table->string('charge_id');
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->string('card_id');
            $table->string('card_last_four');
            $table->string('card_exp_month');
            $table->string('card_exp_year');
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
