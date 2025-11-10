<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;
use Illuminate\Support\Str;

class ExerciseSeeder extends Seeder
{
    public function run()
    {
        $exercises = [
            // Upper Body
            [
                'name' => 'Push-ups',
                'description' => 'Basic bodyweight chest exercise',
                'type' => 'strength',
                'muscle_group' => 'Chest',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Pull-ups',
                'description' => 'Back and biceps exercise using a bar',
                'type' => 'strength',
                'muscle_group' => 'Back',
                'equipment' => 'Pull-up bar'
            ],
            [
                'name' => 'Dips',
                'description' => 'Triceps and chest exercise',
                'type' => 'strength',
                'muscle_group' => 'Arms',
                'equipment' => 'Parallel bars'
            ],

            // Lower Body
            [
                'name' => 'Squats',
                'description' => 'Fundamental leg exercise',
                'type' => 'strength',
                'muscle_group' => 'Legs',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Lunges',
                'description' => 'Unilateral leg exercise',
                'type' => 'strength',
                'muscle_group' => 'Legs',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Calf Raises',
                'description' => 'Isolates calf muscles',
                'type' => 'strength',
                'muscle_group' => 'Legs',
                'equipment' => 'Bodyweight'
            ],

            // Core
            [
                'name' => 'Plank',
                'description' => 'Isometric core exercise',
                'type' => 'core',
                'muscle_group' => 'Core',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Russian Twists',
                'description' => 'Rotational core exercise',
                'type' => 'core',
                'muscle_group' => 'Core',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Leg Raises',
                'description' => 'Lower abdominal exercise',
                'type' => 'core',
                'muscle_group' => 'Core',
                'equipment' => 'Bodyweight'
            ],

            // Cardio
            [
                'name' => 'Burpees',
                'description' => 'Full-body cardio exercise',
                'type' => 'cardio',
                'muscle_group' => 'Full Body',
                'equipment' => 'Bodyweight'
            ],
            [
                'name' => 'Jump Rope',
                'description' => 'Basic cardio exercise',
                'type' => 'cardio',
                'muscle_group' => 'Full Body',
                'equipment' => 'Jump rope'
            ],

            // Weighted Exercises
            [
                'name' => 'Dumbbell Bench Press',
                'description' => 'Chest exercise with dumbbells',
                'type' => 'strength',
                'muscle_group' => 'Chest',
                'equipment' => 'Dumbbells'
            ],
            [
                'name' => 'Barbell Deadlift',
                'description' => 'Compound full-body lift',
                'type' => 'strength',
                'muscle_group' => 'Full Body',
                'equipment' => 'Barbell'
            ]
        ];

        foreach ($exercises as $exercise) {
            Exercise::create([
                'name' => $exercise['name'],
                'slug' => Str::slug($exercise['name']),
                'description' => $exercise['description'],
                'type' => $exercise['type'],
                'muscle_group' => $exercise['muscle_group'],
                'equipment' => $exercise['equipment'],
                'is_approved' => true, // Direct goedkeuren voor seeders
            ]);
        }
    }
}