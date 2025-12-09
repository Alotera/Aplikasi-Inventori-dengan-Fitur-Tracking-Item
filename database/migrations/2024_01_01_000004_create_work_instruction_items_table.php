<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_instruction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_instruction_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('required_quantity')->default(1);
            $table->integer('actual_quantity')->nullable();
            $table->enum('condition', ['good', 'not_good'])->nullable();
            $table->enum('status', ['pending', 'completed', 'not_good'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['work_instruction_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_instruction_items');
    }
};