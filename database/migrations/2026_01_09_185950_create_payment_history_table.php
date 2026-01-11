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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('booking_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_before', 15, 2)->nullable()->comment('Amount paid before this payment');
            $table->decimal('amount_after', 15, 2)->comment('Amount paid after this payment');
            $table->string('payment_method')->nullable()->comment('cash, bank_transfer, online, etc.');
            $table->text('notes')->nullable();
            $table->ulid('recorded_by')->nullable()->comment('User who recorded this payment');
            $table->timestamp('payment_date');
            $table->timestamps();

            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('booking_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
