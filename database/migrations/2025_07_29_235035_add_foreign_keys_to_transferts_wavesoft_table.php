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
        Schema::table('transferts_wavesoft', function (Blueprint $table) {
            $table->foreign(['commande_id'], 'transferts_wavesoft_ibfk_1')->references(['id'])->on('commandes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferts_wavesoft', function (Blueprint $table) {
            $table->dropForeign('transferts_wavesoft_ibfk_1');
        });
    }
};
