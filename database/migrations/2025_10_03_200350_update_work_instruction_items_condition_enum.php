<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_instruction_items', function (Blueprint $table) {
            // Update condition enum to use simplified options for checking type
            $table->enum('condition', ['good', 'not_good'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('work_instruction_items', function (Blueprint $table) {
            // Revert to original condition enum (keep the same values)
            $table->enum('condition', ['good', 'not_good'])->nullable()->change();
        });
    }
};