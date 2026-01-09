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
        Schema::table('conversations', function (Blueprint $table) {
            // Link conversation to campaign recipient (the exact recipient who received the campaign)
            $table->ulid('campaign_recipient_id')->nullable()->after('campaign_id')->comment('The campaign recipient that led to this conversation');
            
            // Index for performance
            $table->index('campaign_recipient_id');
            
            // Foreign key
            $table->foreign('campaign_recipient_id')->references('id')->on('campaign_recipients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['campaign_recipient_id']);
            $table->dropIndex(['campaign_recipient_id']);
            $table->dropColumn('campaign_recipient_id');
        });
    }
};
