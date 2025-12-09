<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('movement_type'); // IN, OUT, TRANSFER, ADJUSTMENT, CHECKING_RESULT, WI_CONSUMPTION
            $table->integer('quantity'); // positive for IN, negative for OUT
            $table->integer('before_quantity')->default(0);
            $table->integer('after_quantity')->default(0);
            $table->string('reference_type')->nullable(); // work_instruction, manual_adjustment, transfer, checking
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the reference record
            $table->foreignId('location_id')->nullable()->constrained('item_locations')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional data like from_location, to_location for transfers
            $table->timestamps();
            
            $table->index(['item_id', 'created_at']);
            $table->index(['movement_type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
