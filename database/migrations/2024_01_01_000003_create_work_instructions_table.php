<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('wi_number')->unique();
            $table->enum('type', ['checking', 'ambil']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_user_id')->constrained('users');
            $table->datetime('deadline');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['not_started', 'completed', 'overdue'])->default('not_started');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_instructions');
    }
};