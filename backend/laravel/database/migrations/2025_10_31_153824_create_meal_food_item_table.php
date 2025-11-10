
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_food_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_food_item');
    }
};
