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
        // Update existing stock movements with 'checking' type to 'CHECKING_RESULT'
        DB::table('stock_movements')
            ->where('movement_type', 'checking')
            ->update(['movement_type' => 'CHECKING_RESULT']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the update: change 'CHECKING_RESULT' back to 'checking'
        DB::table('stock_movements')
            ->where('movement_type', 'CHECKING_RESULT')
            ->update(['movement_type' => 'checking']);
    }
};
