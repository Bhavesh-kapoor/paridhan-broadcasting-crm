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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('phone');                  // Phone number
            $table->date('booking_date');             // Booking date
            $table->string('booking_location', 255);       // Booking location
            $table->string('table_no');               // Table number
            $table->string('price', 100)->nullable();
            $table->string('amount_paid', 100)->nullable();
            $table->string('employee_id');                // Booked by
            $table->timestamps();                     // created_at & updated_at

            // Indexes
            $table->index('phone');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
