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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('code_wavesoft', 20)
                ->nullable()    // NULL autorisé
                ->default(null) // NULL par défaut
                ->unique()     // Unicité pour les valeurs non-NULL
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
             $table->string('code_wavesoft', 20)
              ->nullable(false)
              ->default('')
              ->unique()
              ->change();
        });
    }
};
