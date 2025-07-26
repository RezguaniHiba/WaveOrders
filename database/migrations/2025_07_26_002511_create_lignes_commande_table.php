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
        Schema::create('lignes_commande', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('commande_id')->index('commande_id');
            $table->integer('article_id')->index('article_id');
            $table->integer('quantite');
            $table->decimal('prix_unitaire_ht', 10);
            $table->decimal('taux_tva', 5);
            $table->decimal('remise_percent', 5)->default(0);
            $table->decimal('montant_ht', 12)->nullable()->storedAs('(((`quantite` * `prix_unitaire_ht`) * (100 - `remise_percent`)) / 100)');
            $table->decimal('montant_tva', 12)->nullable()->storedAs('(((((`quantite` * `prix_unitaire_ht`) * (100 - `remise_percent`)) / 100) * `taux_tva`) / 100)');
            $table->enum('statut', ['en_attente', 'reserve', 'prepare', 'livre', 'annule'])->default('en_attente')->index('idx_lignes_statut');
            $table->string('wavesoft_ligne_id', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_commande');
    }
};
