<?php

// database/migrations/2024_05_02_000002_create_meals_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('notes')->nullable();
            $table->time('time');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};