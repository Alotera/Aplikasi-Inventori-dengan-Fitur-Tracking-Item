<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('zone', 191)->nullable();
            $table->string('rack', 191)->nullable();
            $table->string('row', 191)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'zone', 'rack', 'row']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};


