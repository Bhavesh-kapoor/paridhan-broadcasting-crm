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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();

            $table->string('user_id');
            $table->string('status', 50);

            $table->date('next_followup_date')->nullable();
            $table->time('next_followup_time')->nullable();

            $table->text('comment')->nullable();

            $table->string('employee_id')->nullable();

            $table->timestamps(); // created_at + updated_at

            // Indexes
            $table->index('user_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
