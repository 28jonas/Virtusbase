<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('frequency'); // daily, weekly, monthly
            $table->string('category')->nullable();
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->bigInteger('profile_id');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};