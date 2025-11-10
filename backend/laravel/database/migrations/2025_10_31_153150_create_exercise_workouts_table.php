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
        Schema::create('exercise_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained();
            $table->integer('sets')->unsigned();
            $table->integer('reps')->unsigned();
            $table->decimal('weight', 8, 2)->nullable(); // kg/lbs
            $table->integer('duration')->nullable();
            $table->integer('calories_burned')->nullable(); //calories burned per set or rep or workout?
            $table->text('notes')->nullable();
            $table->timestamps();
        });
       /* $table->string('name')->nullable();
        $table->dateTime('scheduled_at')->nullable();
        $table->dateTime('completed_at')->nullable();
        $table->integer('duration')->nullable();
        $table->integer('calories_burned')->nullable();
        $table->text('description')->nullable();
        $table->boolean('completed')->default(false);
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_workout');
    }
};