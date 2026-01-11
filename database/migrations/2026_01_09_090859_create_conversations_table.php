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
        Schema::create('conversations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            
            // Company Context (Exhibitor/Company)
            $table->ulid('exhibitor_id')->comment('The company/exhibitor having the conversation');
            
            // Lead/Visitor Context
            $table->ulid('visitor_id')->nullable()->comment('Visitor/Lead contact ID');
            $table->string('visitor_phone')->nullable()->comment('Phone if visitor not in contacts');
            
            // Employee Context
            $table->ulid('employee_id')->comment('Employee who handled conversation');
            
            // Location & Stall Context
            $table->unsignedBigInteger('location_id')->nullable()->comment('Location where conversation happened');
            $table->unsignedBigInteger('table_id')->nullable()->comment('Stall/Table number');
            
            // Campaign Attribution
            $table->ulid('campaign_id')->nullable()->comment('Source campaign if lead came from campaign');
            
            // Conversation Details
            $table->enum('outcome', ['busy', 'interested', 'materialised'])->comment('Conversation outcome');
            $table->text('notes')->nullable()->comment('Conversation notes/comments');
            $table->timestamp('conversation_date')->useCurrent()->comment('When conversation happened');
            
            // Related Records
            $table->unsignedBigInteger('follow_up_id')->nullable()->comment('Linked follow-up if exists');
            // Note: booking_id type matches bookings.id - if bookings uses ULID, change this to ulid()
            $table->unsignedBigInteger('booking_id')->nullable()->comment('Linked booking if materialised');
            
            $table->timestamps();
            
            // Indexes for Performance
            $table->index('exhibitor_id');
            $table->index('visitor_id');
            $table->index('visitor_phone');
            $table->index('employee_id');
            $table->index('location_id');
            $table->index('table_id');
            $table->index('campaign_id');
            $table->index('conversation_date');
            $table->index('outcome');
            $table->index('follow_up_id');
            $table->index('booking_id');
            
            // Composite Indexes for Common Queries
            $table->index(['exhibitor_id', 'conversation_date']);
            $table->index(['campaign_id', 'outcome']);
            $table->index(['location_id', 'table_id', 'conversation_date']);
            
            // Foreign Keys
            $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('set null');
            $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('set null');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('follow_up_id')->references('id')->on('follow_ups')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
