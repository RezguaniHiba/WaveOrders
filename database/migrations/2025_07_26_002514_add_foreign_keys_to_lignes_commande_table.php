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
        Schema::table('lignes_commande', function (Blueprint $table) {
            $table->foreign(['commande_id'], 'lignes_commande_ibfk_1')->references(['id'])->on('commandes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['article_id'], 'lignes_commande_ibfk_2')->references(['id'])->on('articles')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lignes_commande', function (Blueprint $table) {
            $table->dropForeign('lignes_commande_ibfk_1');
            $table->dropForeign('lignes_commande_ibfk_2');
        });
    }
};
