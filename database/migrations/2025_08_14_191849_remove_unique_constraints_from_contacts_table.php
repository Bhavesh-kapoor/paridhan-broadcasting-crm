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
        Schema::table('contacts', function (Blueprint $table) {
            // Remove unique constraint from phone field
            $table->dropUnique(['phone']);
            
            // Remove unique constraint from gst_number field
            $table->dropUnique(['gst_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Add back unique constraint to phone field
            $table->unique('phone');
            
            // Add back unique constraint to gst_number field
            $table->unique('gst_number');
        });
    }
};
