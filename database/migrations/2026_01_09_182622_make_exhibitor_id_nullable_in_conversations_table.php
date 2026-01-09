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
            // Make exhibitor_id nullable
            $table->ulid('exhibitor_id')->nullable()->change();
            
            // Drop the existing foreign key constraint
            $table->dropForeign(['exhibitor_id']);
        });
        
        // Re-add the foreign key with proper nullable constraint
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['exhibitor_id']);
            
            // Make exhibitor_id NOT nullable again
            $table->ulid('exhibitor_id')->nullable(false)->change();
        });
        
        // Re-add the foreign key
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }
};
