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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_id')->unique()->nullable()->comment('WhatsApp API Template ID');
            $table->string('name');
            $table->string('language', 10)->default('en');
            $table->enum('category', ['MARKETING', 'UTILITY', 'AUTHENTICATION'])->default('UTILITY');
            $table->enum('status', ['APPROVED', 'PENDING', 'REJECTED', 'PAUSED'])->default('PENDING');
            $table->text('components')->nullable()->comment('JSON encoded template components');
            $table->boolean('allow_category_change')->default(false);
            $table->timestamp('synced_at')->nullable()->comment('Last sync from API');
            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
