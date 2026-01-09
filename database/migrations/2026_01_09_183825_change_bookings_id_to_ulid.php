<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop foreign keys that reference bookings.id
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropForeign(['booking_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist or might have different name
        }

        // Remove AUTO_INCREMENT and change to ULID (CHAR(26))
        // We need to do this in SQL directly since Laravel doesn't handle this well
        DB::statement('ALTER TABLE bookings MODIFY id CHAR(26) NOT NULL');
        
        // Ensure primary key is set
        DB::statement('ALTER TABLE bookings DROP PRIMARY KEY, ADD PRIMARY KEY (id)');

        // Re-add foreign key
        try {
            Schema::table('conversations', function (Blueprint $table) {
                // First, change booking_id to CHAR(26) to match bookings.id
                DB::statement('ALTER TABLE conversations MODIFY booking_id CHAR(26) NULL');
                
                // Then add foreign key
                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Ignore if foreign key already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropForeign(['booking_id']);
            });
            
            // Change booking_id back to unsignedBigInteger
            DB::statement('ALTER TABLE conversations MODIFY booking_id BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {
            // Ignore
        }

        // Change bookings.id back to BIGINT AUTO_INCREMENT
        DB::statement('ALTER TABLE bookings DROP PRIMARY KEY');
        DB::statement('ALTER TABLE bookings MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE bookings ADD PRIMARY KEY (id)');

        // Re-add foreign key
        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
