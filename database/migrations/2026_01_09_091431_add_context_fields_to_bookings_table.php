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
        Schema::table('bookings', function (Blueprint $table) {
            // Company & Visitor Context
            $table->ulid('exhibitor_id')->nullable()->after('phone')->comment('Company/Exhibitor');
            $table->ulid('visitor_id')->nullable()->after('exhibitor_id')->comment('Visitor/Lead contact');
            
            // Convert location from string to FK (requires data migration)
            $table->unsignedBigInteger('location_id')->nullable()->after('visitor_id');
            
            // Convert table_no from string to FK (requires data migration)
            $table->unsignedBigInteger('table_id')->nullable()->after('location_id');
            
            // Campaign Attribution
            $table->ulid('campaign_id')->nullable()->after('table_id')->comment('Source campaign');
            
            // Conversation Link
            $table->ulid('conversation_id')->nullable()->after('campaign_id');
            
            // Convert price fields to decimal for proper calculations
            $table->decimal('price', 15, 2)->nullable()->change();
            $table->decimal('amount_paid', 15, 2)->nullable()->change();
            
            // Keep old fields for backward compatibility (can drop in future migration)
            // booking_location and table_no remain as strings for now
            
            // Indexes
            $table->index('exhibitor_id');
            $table->index('visitor_id');
            $table->index('location_id');
            $table->index('table_id');
            $table->index('campaign_id');
            $table->index('conversation_id');
            $table->index(['campaign_id', 'amount_paid']);
            
            // Foreign Keys
            $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('restrict');
            $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('restrict');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['exhibitor_id']);
            $table->dropForeign(['visitor_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['table_id']);
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['conversation_id']);
            
            // Drop indexes
            $table->dropIndex(['campaign_id', 'amount_paid']);
            $table->dropIndex(['conversation_id']);
            $table->dropIndex(['campaign_id']);
            $table->dropIndex(['table_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['visitor_id']);
            $table->dropIndex(['exhibitor_id']);
            
            // Revert price fields to string (if needed)
            $table->string('price', 100)->nullable()->change();
            $table->string('amount_paid', 100)->nullable()->change();
            
            // Drop columns
            $table->dropColumn([
                'exhibitor_id',
                'visitor_id',
                'location_id',
                'table_id',
                'campaign_id',
                'conversation_id',
            ]);
        });
    }
};
