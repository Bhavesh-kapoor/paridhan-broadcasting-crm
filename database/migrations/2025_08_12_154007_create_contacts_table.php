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
        Schema::create('contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('location')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('product_type')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('gst_number')->unique()->nullable();
            $table->string('type')->default('visitor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
