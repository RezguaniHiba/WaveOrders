<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggersStatut extends Migration
{
    public function up()
    {
        // Trigger UPDATE statut
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_statut_commande_after_update;
            CREATE TRIGGER trg_update_statut_commande_after_update
            AFTER UPDATE ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE nb_total INT DEFAULT 0;
                DECLARE nb_livree INT DEFAULT 0;
                DECLARE nb_en_cours INT DEFAULT 0;
                DECLARE nb_annulee INT DEFAULT 0;
                DECLARE nb_consignation INT DEFAULT 0;
                DECLARE v_commande_id INT;
                
                IF @STATUT_DISABLED IS NULL AND (OLD.statut != NEW.statut OR OLD.quantite != NEW.quantite) THEN
                    SET @STATUT_DISABLED = 1;
                    SET v_commande_id = NEW.commande_id;
                    
                    SELECT COUNT(*),
                        SUM(statut = "livre"),
                        SUM(statut IN ("reserve", "en_consigne", "prepare")),
                        SUM(statut = "annule"),
                        SUM(statut = "en_consigne")
                    INTO nb_total, nb_livree, nb_en_cours, nb_annulee, nb_consignation
                    FROM lignes_commande 
                    WHERE commande_id = v_commande_id;
                    
                    IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                        SET @COMMANDES_UPDATE_DISABLED = 1;
                        
                        UPDATE commandes 
                        SET statut = CASE
                            WHEN nb_annulee = nb_total THEN "annulee"
                            WHEN nb_livree = nb_total THEN "complètement_livree"
                            WHEN nb_consignation = nb_total THEN "consignation"
                            WHEN nb_livree > 0 AND nb_livree < nb_total THEN "partiellement_livree"
                            WHEN nb_en_cours > 0 THEN "en_cours_de_traitement"
                            ELSE "brouillon"
                        END
                        WHERE id = v_commande_id;
                        
                        SET @COMMANDES_UPDATE_DISABLED = NULL;
                    END IF;
                    
                    SET @STATUT_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger INSERT statut
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_statut_commande_after_insert;
            CREATE TRIGGER trg_update_statut_commande_after_insert
            AFTER INSERT ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE nb_total INT DEFAULT 0;
                DECLARE nb_livree INT DEFAULT 0;
                DECLARE nb_en_cours INT DEFAULT 0;
                DECLARE nb_annulee INT DEFAULT 0;
                DECLARE nb_consignation INT DEFAULT 0;
                DECLARE v_commande_id INT;
                
                IF @STATUT_DISABLED IS NULL THEN
                    SET @STATUT_DISABLED = 1;
                    SET v_commande_id = NEW.commande_id;
                    
                    SELECT COUNT(*) INTO nb_total 
                    FROM lignes_commande 
                    WHERE commande_id = v_commande_id;

                    SELECT SUM(statut = "livre"),
                        SUM(statut IN ("reserve", "en_consigne", "prepare")),
                        SUM(statut = "annule"),
                        SUM(statut = "en_consigne")
                    INTO nb_livree, nb_en_cours, nb_annulee, nb_consignation
                    FROM lignes_commande 
                    WHERE commande_id = v_commande_id;
                    
                    IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                        SET @COMMANDES_UPDATE_DISABLED = 1;
                        
                        IF nb_annulee = nb_total THEN
                            UPDATE commandes SET statut = "annulee" WHERE id = v_commande_id;
                        ELSEIF nb_livree = nb_total THEN
                            UPDATE commandes SET statut = "complètement_livree" WHERE id = v_commande_id;
                        ELSEIF nb_consignation = nb_total THEN
                            UPDATE commandes SET statut = "consignation" WHERE id = v_commande_id;
                        ELSEIF nb_livree > 0 AND nb_livree < nb_total THEN
                            UPDATE commandes SET statut = "partiellement_livree" WHERE id = v_commande_id;
                        ELSEIF nb_en_cours > 0 THEN
                            UPDATE commandes SET statut = "en_cours_de_traitement" WHERE id = v_commande_id;
                        ELSE
                            UPDATE commandes SET statut = "brouillon" WHERE id = v_commande_id;
                        END IF;
                        
                        SET @COMMANDES_UPDATE_DISABLED = NULL;
                    END IF;
                    
                    SET @STATUT_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger DELETE statut
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_statut_commande_after_delete;
            CREATE TRIGGER trg_update_statut_commande_after_delete
            AFTER DELETE ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE nb_total INT DEFAULT 0;
                DECLARE nb_livree INT DEFAULT 0;
                DECLARE nb_en_cours INT DEFAULT 0;
                DECLARE nb_annulee INT DEFAULT 0;
                DECLARE nb_consignation INT DEFAULT 0;
                DECLARE v_commande_id INT;
                
                IF @STATUT_DELETE_DISABLED IS NULL THEN
                    SET @STATUT_DELETE_DISABLED = 1;
                    SET v_commande_id = OLD.commande_id;
                    
                    SELECT COUNT(*) INTO nb_total 
                    FROM lignes_commande 
                    WHERE commande_id = v_commande_id;

                    IF nb_total = 0 THEN
                        -- Si aucune ligne, on remet la commande en brouillon
                        IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                            SET @COMMANDES_UPDATE_DISABLED = 1;
                            UPDATE commandes SET statut = "brouillon" WHERE id = v_commande_id;
                            SET @COMMANDES_UPDATE_DISABLED = NULL;
                        END IF;
                    ELSE
                        SELECT SUM(statut = "livre"),
                            SUM(statut IN ("reserve", "en_consigne", "prepare")),
                            SUM(statut = "annule"),
                            SUM(statut = "en_consigne")
                        INTO nb_livree, nb_en_cours, nb_annulee, nb_consignation
                        FROM lignes_commande 
                        WHERE commande_id = v_commande_id;
                             -- Mettre à jour le statut de la commande parente
                        IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                            SET @COMMANDES_UPDATE_DISABLED = 1;
                            
                            UPDATE commandes 
                            SET statut = CASE
                                WHEN nb_annulee = nb_total THEN "annulee"
                                WHEN nb_livree = nb_total THEN "complètement_livree"
                                WHEN nb_consignation = nb_total THEN "consignation"
                                WHEN nb_livree > 0 THEN "partiellement_livree"
                                WHEN nb_en_cours > 0 THEN "en_cours_de_traitement"
                                ELSE "brouillon"
                            END
                            WHERE id = v_commande_id;
                            
                            SET @COMMANDES_UPDATE_DISABLED = NULL;
                        END IF;
                    END IF;
                    
                    SET @STATUT_DELETE_DISABLED = NULL;
                END IF;
            END
        ');
    }
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_statut_commande_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_statut_commande_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_statut_commande_after_delete');
    }
}