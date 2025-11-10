<?php
// database/migrations/2024_05_02_000001_create_meal_types_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Voeg standaard maaltijdtypes toe
        DB::table('meal_types')->insert([
            ['name' => 'Ontbijt', 'description' => 'Ochtendmaaltijd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tussendoortje (ochtend)', 'description' => 'Snack tussen ontbijt en lunch', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lunch', 'description' => 'Middagmaaltijd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tussendoortje (middag)', 'description' => 'Snack tussen lunch en diner', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Diner', 'description' => 'Avondmaaltijd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tussendoortje (avond)', 'description' => 'Snack na het diner', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_types');
    }
};