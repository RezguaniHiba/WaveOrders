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
            $table->id(); // Equivalent à INT AUTO_INCREMENT PRIMARY KEY
            $table->integer('ligne_commande_id')->index('idx_mouvement_commande');
            $table->foreign('ligne_commande_id')->references('id')->on('lignes_commande')->onUpdate('cascade')->onDelete('cascade');
            
            $table->integer('quantite'); // Valeur signée (-sortie, +entrée)
            
            $table->enum('type_mouvement', [
                'reservation',
                'annulation',
                'livraison',
                'ajout_manuel',
                'inventaire',
                'consignation_sortie',
                'consignation_retour',
                'consignation_vente',
                'consignation_ajustement'
            ]);
            
            $table->dateTime('date_mouvement')
                  ->useCurrent(); // DEFAULT CURRENT_TIMESTAMP
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