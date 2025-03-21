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
        Schema::table('parking', function (Blueprint $table) {
            $table->renameColumn('avai;able_prices', 'available_places');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking', function (Blueprint $table) {
            $table->renameColumn('available_places', 'avai;able_prices');
        });
    }
};
