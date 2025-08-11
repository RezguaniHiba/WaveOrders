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
        Schema::create('commandes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('numero', 20)->unique('numero');
            $table->integer('client_id')->index('idx_commandes_client');
            $table->integer('commercial_id')->nullable()->index('idx_commandes_commercial');
            $table->integer('cree_par')->nullable()->index('idx_commandes_createur');
            $table->dateTime('date_commande')->useCurrent();
            $table->date('date_livraison_prevue')->nullable();
            $table->enum('statut', ['brouillon','en_cours_de_traitement','consignation','partiellement_livree','complètement_livree','annulee'])->default('brouillon')->index('idx_commandes_statut');
            $table->decimal('montant_ht', 12)->nullable()->default(0);
            $table->decimal('montant_tva', 12)->nullable()->default(0);
            $table->decimal('montant_ttc', 12)->nullable()->default(0);
            $table->decimal('remise_percent', 5)->nullable()->default(0);
            $table->decimal('remise_montant', 10)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->string('wavesoft_piece_id', 20)->nullable();
            $table->dateTime('date_export_wavesoft')->nullable();
            $table->dateTime('date_maj')->useCurrentOnUpdate()->nullable();

            $table->index(['statut', 'client_id'], 'idx_commandes_statut_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
