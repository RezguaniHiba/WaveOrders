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
        Schema::table('historique_commandes', function (Blueprint $table) {
            $table->foreign(['commande_id'], 'historique_commandes_ibfk_1')->references(['id'])->on('commandes')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_commandes', function (Blueprint $table) {
            $table->dropForeign('historique_commandes_ibfk_1');
        });
    }
};
