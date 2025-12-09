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
        Schema::create('work_instruction_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_instruction_id')->constrained()->onDelete('cascade');
            $table->enum('status_progress', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('status_ke_tuntasan')->default(0); // 0-100 percentage
            $table->text('notes')->nullable();
            $table->timestamp('status_updated_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['work_instruction_id', 'status_progress'], 'wi_status_progress_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_instruction_status');
    }
};
