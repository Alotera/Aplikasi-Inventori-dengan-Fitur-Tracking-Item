<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->string('destination_line')->nullable()->after('description');
            $table->text('dropoff_notes')->nullable()->after('destination_line');
        });
    }

    public function down(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->dropColumn(['destination_line', 'dropoff_notes']);
        });
    }
};



