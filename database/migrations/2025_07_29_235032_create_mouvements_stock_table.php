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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ligne_commande_id')->index('ligne_commande_id');
            $table->integer('quantite');
            $table->enum('type_mouvement', ['reservation', 'annulation', 'livraison', 'ajout_manuel', 'inventaire', 'consignation_sortie', 'consignation_retour', 'consignation_vente', 'consignation_ajustement']);
            $table->dateTime('date_mouvement')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
