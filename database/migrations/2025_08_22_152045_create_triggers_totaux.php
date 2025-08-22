<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggersTotaux extends Migration
{
    public function up()
    {
        // Trigger AFTER UPDATE
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_totaux_update;
            CREATE TRIGGER trg_update_totaux_update
            AFTER UPDATE ON lignes_commande
            FOR EACH ROW
            BEGIN
                    DECLARE v_commande_id INT;
                    DECLARE v_total_ht, v_total_tva DECIMAL(12,2);
                IF @TOTAUX_DISABLED IS NULL THEN
                    SET @TOTAUX_DISABLED = 1;
                
                    SET v_commande_id = NEW.commande_id;
                    SELECT COALESCE(SUM(montant_ht), 0), COALESCE(SUM(montant_tva), 0)
                    INTO v_total_ht, v_total_tva
                    FROM lignes_commande
                    WHERE commande_id = v_commande_id;

                    IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                        SET @COMMANDES_UPDATE_DISABLED = 1;
                        UPDATE commandes SET 
                            montant_ht = v_total_ht,
                            montant_tva = v_total_tva,
                            montant_ttc = v_total_ht + v_total_tva,
                            date_maj = CURRENT_TIMESTAMP
                        WHERE id = v_commande_id;
                        SET @COMMANDES_UPDATE_DISABLED = NULL;
                    END IF;

                    IF NEW.statut IS NOT NULL AND (OLD.statut IS NULL OR NEW.statut != OLD.statut) THEN
                        INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                        VALUES ("commande", v_commande_id, "update");
                    END IF;
                    
                    SET @TOTAUX_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger AFTER INSERT
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_totaux_insert;
            CREATE TRIGGER trg_update_totaux_insert
            AFTER INSERT ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE v_commande_id INT;
                DECLARE v_total_ht, v_total_tva DECIMAL(12,2);
                
                IF @TOTAUX_DISABLED IS NULL THEN
                    SET @TOTAUX_DISABLED = 1;
                    SET v_commande_id = NEW.commande_id;
                    
                    SELECT COALESCE(SUM(montant_ht), 0), COALESCE(SUM(montant_tva), 0)
                    INTO v_total_ht, v_total_tva
                    FROM lignes_commande
                    WHERE commande_id = v_commande_id;

                    IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                        SET @COMMANDES_UPDATE_DISABLED = 1;
                        UPDATE commandes SET 
                            montant_ht = v_total_ht,
                            montant_tva = v_total_tva,
                            montant_ttc = v_total_ht + v_total_tva,
                            date_maj = CURRENT_TIMESTAMP
                        WHERE id = v_commande_id;
                        SET @COMMANDES_UPDATE_DISABLED = NULL;
                    END IF;

                    IF NEW.statut IS NOT NULL THEN
                        INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                        VALUES ("commande", v_commande_id, "create");
                    END IF;
                    
                    SET @TOTAUX_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger AFTER DELETE
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_totaux_delete;
            CREATE TRIGGER trg_update_totaux_delete
            AFTER DELETE ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE v_commande_id INT;
                DECLARE v_total_ht, v_total_tva DECIMAL(12,2);
                
                IF @TOTAUX_DISABLED IS NULL THEN
                    SET @TOTAUX_DISABLED = 1;
                    SET v_commande_id = OLD.commande_id;
                    
                    SELECT COALESCE(SUM(montant_ht), 0), COALESCE(SUM(montant_tva), 0)
                    INTO v_total_ht, v_total_tva
                    FROM lignes_commande
                    WHERE commande_id = v_commande_id;

                    IF @COMMANDES_UPDATE_DISABLED IS NULL THEN
                        SET @COMMANDES_UPDATE_DISABLED = 1;
                        UPDATE commandes SET 
                            montant_ht = v_total_ht,
                            montant_tva = v_total_tva,
                            montant_ttc = v_total_ht + v_total_tva,
                            date_maj = CURRENT_TIMESTAMP
                        WHERE id = v_commande_id;
                        SET @COMMANDES_UPDATE_DISABLED = NULL;
                    END IF;
                    
                    SET @TOTAUX_DISABLED = NULL;
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_totaux_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_totaux_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_totaux_delete');
    }
}