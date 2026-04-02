<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->string('completion_evidence_path')->nullable()->after('notes');
        });

        Schema::table('work_instruction_items', function (Blueprint $table) {
            $table->string('discrepancy_evidence_path')->nullable()->after('notes');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE work_instruction_items MODIFY COLUMN status ENUM('pending','completed','not_good','not_found') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->dropColumn('completion_evidence_path');
        });

        Schema::table('work_instruction_items', function (Blueprint $table) {
            $table->dropColumn('discrepancy_evidence_path');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE work_instruction_items MODIFY COLUMN status ENUM('pending','completed','not_good') NOT NULL DEFAULT 'pending'");
        }
    }
};
