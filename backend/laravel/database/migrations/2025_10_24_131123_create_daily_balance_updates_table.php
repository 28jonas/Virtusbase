<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('daily_balance_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bankingcard_id')->constrained('bankingcards'); // Explicitere referentie
            $table->date('snapshot_date');
            $table->decimal('balance', 12, 2);
            $table->timestamps();

            $table->unique(['bankingcard_id', 'snapshot_date']); // Voeg dit toe voor dubbele entries te voorkomen
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_balance_updates');
    }
};