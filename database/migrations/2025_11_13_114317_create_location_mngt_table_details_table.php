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
        Schema::create('location_mngt_table_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_mngt_id'); // Foreign key to location_mngt
            $table->string('table_no', 50);
            $table->string('table_size', 100)->nullable();
            $table->string('price', 100)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('location_mngt_id')
                ->references('id')
                ->on('location_mngt')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_mngt_table_details');
    }
};
