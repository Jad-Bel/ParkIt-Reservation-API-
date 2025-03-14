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
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table `users`
            $table->foreignId('parking_id')->constrained('parking')->onDelete('cascade'); // Clé étrangère vers la table `parkings`
            $table->dateTime('arrival_time'); // Heure d'arrivée
            $table->dateTime('departure_time'); // Heure de départ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
        });
    }
};
