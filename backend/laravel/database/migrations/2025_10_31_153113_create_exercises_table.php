<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /*activated/validated toevoegen om als admin te excersises te kunnen toestaan */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Bijv. "Push-up"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('type')->nullable(); //Later make enum
            $table->enum('muscle_group', ['Chest', 'Back', 'Legs', 'Shoulders', 'Arms', 'Core', 'Full Body', ]);
            $table->text('equipment')->nullable(); //Later make enum
            $table->boolean('is_approved')->default(false);
            $table->foreignId('submitted_by')->nullable()->constrained('profiles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};