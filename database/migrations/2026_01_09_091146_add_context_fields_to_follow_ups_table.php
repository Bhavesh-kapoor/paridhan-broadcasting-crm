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
        Schema::table('follow_ups', function (Blueprint $table) {
            // Company & Visitor Context
            $table->ulid('exhibitor_id')->nullable()->after('phone')->comment('Company/Exhibitor context');
            $table->ulid('visitor_id')->nullable()->after('exhibitor_id')->comment('Visitor/Lead contact ID');
            
            // Location & Stall Context
            $table->unsignedBigInteger('location_id')->nullable()->after('visitor_id');
            $table->unsignedBigInteger('table_id')->nullable()->after('location_id');
            
            // Campaign Attribution
            $table->ulid('campaign_id')->nullable()->after('table_id')->comment('Source campaign ID');
            
            // Conversation Link
            $table->ulid('conversation_id')->nullable()->after('campaign_id')->comment('Linked conversation');
            
            // Indexes
            $table->index('exhibitor_id');
            $table->index('visitor_id');
            $table->index('location_id');
            $table->index('table_id');
            $table->index('campaign_id');
            $table->index('conversation_id');
            
            // Composite Index
            $table->index(['exhibitor_id', 'status', 'created_at']);
            
            // Foreign Keys
            $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('set null');
            $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('set null');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['exhibitor_id']);
            $table->dropForeign(['visitor_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['table_id']);
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['conversation_id']);
            
            // Drop indexes
            $table->dropIndex(['exhibitor_id', 'status', 'created_at']);
            $table->dropIndex(['conversation_id']);
            $table->dropIndex(['campaign_id']);
            $table->dropIndex(['table_id']);
            $table->dropIndex(['location_id']);
            $table->dropIndex(['visitor_id']);
            $table->dropIndex(['exhibitor_id']);
            
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
