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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('date_of_birth');
            $table->string('position')->nullable()->after('status');
            $table->decimal('salary', 10, 2)->nullable()->after('position');
            $table->date('hire_date')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'status', 'position', 'salary', 'hire_date']);
        });
    }
};
