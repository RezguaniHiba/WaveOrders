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
        Schema::dropIfExists('reglements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('reglements', function (Blueprint $table) {
            $table->id();  
            // Clé étrangère vers commande
            $table->unsignedBigInteger('commande_id');
            $table->foreign('commande_id')->references('id')->on('commandes')->onDelete('cascade');
            // Montant payé (obligatoire)
            $table->decimal('montant', 12, 2);

            // Type de règlement : chèque, virement, espèces, etc.
            $table->enum('mode', ['especes', 'cheque', 'carte_bancaire', 'virement', 'autre'])->default('cheque');
            // Pièce jointe (chemin vers photo du règlement)
            $table->string('fichier_justificatif')->nullable();

            // Type de facturation
            $table->enum('type_facturation', ['facturer_client', 'client_payeur', 'autre'])->default('facturer_client');
            //date de reglement
            $table->date('date_reglement')->nullable();
            // Si client payeur différent
            $table->unsignedBigInteger('client_payeur_id')->nullable();
            $table->foreign('client_payeur_id')->references('id')->on('clients')->onDelete('set null');
            // Commentaire (cas "autre" ou infos complémentaires)
            $table->text('commentaire')->nullable();
            // Qui a saisi le règlement
            $table->unsignedBigInteger('cree_par')->nullable();
            $table->foreign('cree_par')->references('id')->on('utilisateurs')->onDelete('set null');
            // Référence de synchronisation avec WaveSoft
            $table->string('wavesoft_ref')->nullable();
            //index 
            $table->index(['date_reglement', 'commande_id']);

            // Timestamps Laravel
            $table->timestamps();
        });
    }
};

