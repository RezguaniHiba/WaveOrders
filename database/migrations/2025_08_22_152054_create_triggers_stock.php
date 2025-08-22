<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggersStock extends Migration
{
    public function up()
    {
        // Trigger UPDATE stock
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_gestion_stock_update;
            CREATE TRIGGER trg_gestion_stock_update
            AFTER UPDATE ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE v_article_id INT;
                DECLARE v_ancien_impact_stock BOOLEAN;
                DECLARE v_nouveau_impact_stock BOOLEAN;
                
                IF @STOCK_DISABLED IS NULL THEN
                    SET @STOCK_DISABLED = 1;
                    SET v_article_id = NEW.article_id;
                    
                    SET v_ancien_impact_stock = (OLD.statut IN ("reserve", "en_consigne"));
                    SET v_nouveau_impact_stock = (NEW.statut IN ("reserve", "en_consigne", "annulee"));
                    
                    IF (OLD.statut != NEW.statut AND (v_ancien_impact_stock OR v_nouveau_impact_stock)) 
                       OR (OLD.quantite != NEW.quantite AND v_nouveau_impact_stock) THEN
                        
                        IF OLD.statut != NEW.statut AND v_ancien_impact_stock THEN
                            IF OLD.statut = "reserve" THEN
                                UPDATE articles 
                                SET stock_disponible = stock_disponible + OLD.quantite,
                                    stock_reserve = stock_reserve - OLD.quantite
                                WHERE id = v_article_id;
                                
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, OLD.quantite, "annulation");
                            
                            ELSEIF OLD.statut = "en_consigne" THEN
                                UPDATE articles 
                                SET stock_disponible = stock_disponible + OLD.quantite,
                                    stock_consigne = stock_consigne - OLD.quantite
                                WHERE id = v_article_id;
                                
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, OLD.quantite, "consignation_retour");
                            END IF;
                        END IF;
                        
                        IF NEW.statut = "annulee" THEN
                            BEGIN END;
                        ELSEIF NEW.statut = "en_consigne" THEN
                            UPDATE articles 
                            SET stock_disponible = stock_disponible - NEW.quantite,
                                stock_consigne = stock_consigne + NEW.quantite
                            WHERE id = v_article_id;
                            
                            IF OLD.statut = "en_consigne" AND OLD.quantite != NEW.quantite THEN
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, -NEW.quantite, "consignation_ajustement");
                            ELSE
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, -NEW.quantite, "consignation_sortie");
                            END IF;
                        ELSEIF NEW.statut = "reserve" THEN
                            UPDATE articles 
                            SET stock_disponible = stock_disponible - NEW.quantite,
                                stock_reserve = stock_reserve + NEW.quantite
                            WHERE id = v_article_id;
                            
                            INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                            VALUES (NEW.id, -NEW.quantite, "reservation");
                        ELSEIF NEW.statut = "livre" THEN
                            IF OLD.statut = "reserve" THEN
                                UPDATE articles 
                                SET stock_reserve = stock_reserve - OLD.quantite
                                WHERE id = v_article_id;
                                
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, -OLD.quantite, "livraison");
                            ELSEIF OLD.statut = "en_consigne" THEN
                                UPDATE articles 
                                SET stock_consigne = stock_consigne - OLD.quantite
                                WHERE id = v_article_id;
                                
                                INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                                VALUES (NEW.id, -OLD.quantite, "consignation_vente");
                            END IF;
                        END IF;
                        
                        UPDATE articles SET date_maj_stock = NOW() WHERE id = v_article_id;
                        
                        INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                        VALUES ("article", v_article_id, "update")
                        ON DUPLICATE KEY UPDATE action = VALUES(action), statut = "pending", tentatives = 0;
                    END IF;
                    
                    SET @STOCK_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger INSERT stock
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_gestion_stock_insert;
            CREATE TRIGGER trg_gestion_stock_insert
            AFTER INSERT ON lignes_commande
            FOR EACH ROW
            BEGIN
                DECLARE v_article_statut VARCHAR(20);
                
                IF @STOCK_DISABLED IS NULL THEN
                    SET @STOCK_DISABLED = 1;
                    SET v_article_statut = NEW.statut;
                    
                    IF v_article_statut = "en_consigne" THEN
                        UPDATE articles 
                        SET stock_disponible = stock_disponible - NEW.quantite,
                            stock_consigne = stock_consigne + NEW.quantite,
                            date_maj_stock = NOW()
                        WHERE id = NEW.article_id;
                        
                        INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                        VALUES (NEW.id, -NEW.quantite, "consignation_sortie");
                        
                        INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                        VALUES ("article", NEW.article_id, "create")
                        ON DUPLICATE KEY UPDATE action = VALUES(action), statut = "pending", tentatives = 0;
                        
                    ELSEIF v_article_statut = "reserve" THEN
                        UPDATE articles 
                        SET stock_disponible = stock_disponible - NEW.quantite,
                            stock_reserve = stock_reserve + NEW.quantite,
                            date_maj_stock = NOW()
                        WHERE id = NEW.article_id;
                        
                        INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                        VALUES (NEW.id, -NEW.quantite, "reservation");

                        INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                        VALUES ("article", NEW.article_id, "create")
                        ON DUPLICATE KEY UPDATE action = VALUES(action), statut = "pending", tentatives = 0;
                    END IF;
                    
                    SET @STOCK_DISABLED = NULL;
                END IF;
            END
        ');

        // Trigger DELETE stock
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_gestion_stock_delete;
            CREATE TRIGGER trg_gestion_stock_delete
            AFTER DELETE ON lignes_commande
            FOR EACH ROW
            BEGIN
                IF @STOCK_DISABLED IS NULL THEN
                    SET @STOCK_DISABLED = 1;
                    IF OLD.statut = "en_consigne" THEN
                        UPDATE articles 
                        SET stock_disponible = stock_disponible + OLD.quantite,
                            stock_consigne = stock_consigne - OLD.quantite,
                            date_maj_stock = NOW()
                        WHERE id = OLD.article_id;

                        INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                        VALUES (OLD.id, OLD.quantite, "consignation_retour");
                    
                    ELSEIF OLD.statut = "reserve" THEN
                        UPDATE articles 
                        SET stock_disponible = stock_disponible + OLD.quantite,
                            stock_reserve = stock_reserve - OLD.quantite,
                            date_maj_stock = NOW()
                        WHERE id = OLD.article_id;
                        
                        INSERT INTO mouvements_stock (ligne_commande_id, quantite, type_mouvement)
                        VALUES (OLD.id, OLD.quantite, "annulation");
                    END IF;

                    INSERT INTO sync_wavesoft (entite_type, entite_id, action)
                    VALUES ("stock", OLD.article_id, "delete")
                    ON DUPLICATE KEY UPDATE action = VALUES(action), statut = "pending", tentatives = 0;
                    
                    SET @STOCK_DISABLED = NULL;
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_gestion_stock_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_gestion_stock_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_gestion_stock_delete');
    }
}