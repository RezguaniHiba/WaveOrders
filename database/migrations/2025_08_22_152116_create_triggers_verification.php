<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggersVerification extends Migration
{
    public function up()
    {
        // Trigger vérification cohérence statut
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_verif_coherence_statut;
            CREATE TRIGGER trg_verif_coherence_statut
            BEFORE UPDATE ON commandes
            FOR EACH ROW
            BEGIN
                DECLARE nb_total INT;
                DECLARE nb_livree INT;
                DECLARE nb_annulee INT;
                DECLARE nb_consignation INT;
                DECLARE nb_en_cours INT;
                
                -- Vérification uniquement si le statut change
                IF NEW.statut != OLD.statut THEN
                    -- Récupère les statistiques actuelles des lignes
                    SELECT 
                        COUNT(*),
                        SUM(statut = "livre"),
                        SUM(statut = "annule"),
                        SUM(statut = "en_consigne"),
                        SUM(statut IN ("reserve", "prepare", "en_consigne"))
                    INTO 
                        nb_total,
                        nb_livree,
                        nb_annulee,
                        nb_consignation,
                        nb_en_cours
                    FROM lignes_commande
                    WHERE commande_id = NEW.id;
                    
                    -- Vérifie la cohérence avant modification
                    IF NOT (
                        (NEW.statut = "annulee" AND nb_annulee = nb_total) OR
                        (NEW.statut = "complètement_livree" AND nb_livree = nb_total) OR
                        (NEW.statut = "consignation" AND nb_consignation = nb_total) OR
                        (NEW.statut = "partiellement_livree" AND nb_livree > 0 AND nb_livree < nb_total) OR
                        (NEW.statut = "en_cours_de_traitement" AND nb_en_cours > 0) OR
                        (NEW.statut = "brouillon" AND nb_total = 0)
                    ) THEN
                        SIGNAL SQLSTATE "45000"
                        SET MESSAGE_TEXT = "Statut de commande incohérent avec les lignes associées";
                    END IF;
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_verif_coherence_statut');
    }
}