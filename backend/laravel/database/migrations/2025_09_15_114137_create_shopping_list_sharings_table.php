<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_list_sharings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('permission_level'); // 'view', 'edit'
            $table->timestamps();
            $table->unique(['shopping_list_id', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_list_sharings');
    }
};