<?php

// database/migrations/2024_05_02_000003_create_food_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->decimal('serving_size', 8, 2);
            $table->string('serving_unit');
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->default(0);
            $table->decimal('fiber', 8, 2)->nullable();
            $table->decimal('sugar', 8, 2)->nullable();
            $table->decimal('sodium', 8, 2)->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};