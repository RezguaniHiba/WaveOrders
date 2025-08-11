<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lignes_commande', function (Blueprint $table) {
            // Suppression sécurisée de l'index
            if ($this->indexExists('lignes_commande', 'idx_lignes_statut')) {
                $table->dropIndex('idx_lignes_statut');
            }
            
            // Modification du enum
            $table->enum('statut', [
                'en_attente',
                'reserve',
                'prepare',
                'livre',
                'annule',
                'en_consigne'
            ])
            ->default('en_attente')
            ->change();
        });

        // Recréation de l'index
        Schema::table('lignes_commande', function (Blueprint $table) {
            $table->index('statut', 'idx_lignes_statut');
        });
    }

    public function down(): void
    {
        Schema::table('lignes_commande', function (Blueprint $table) {
            // Suppression sécurisée de l'index
            if ($this->indexExists('lignes_commande', 'idx_lignes_statut')) {
                $table->dropIndex('idx_lignes_statut');
            }
            
            // Retour à l'ancien enum
            $table->enum('statut', [
                'en_attente',
                'reserve',
                'prepare',
                'livre',
                'annule'
            ])
            ->default('en_attente')
            ->change();
            
            // Recréation de l'index
            $table->index('statut', 'idx_lignes_statut');
        });
    }

    /**
     * Vérifie si un index existe
     */
    protected function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        return (bool) $connection->selectOne(
            "SELECT COUNT(*) AS exists_flag 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$database, $table, $index]
        )->exists_flag;
    }
};