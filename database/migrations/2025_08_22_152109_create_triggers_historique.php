<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggersHistorique extends Migration
{
    public function up()
    {
        // Trigger historique des commandes
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_historique_commandes;
            CREATE TRIGGER trg_historique_commandes
            AFTER UPDATE ON commandes
            FOR EACH ROW
            BEGIN
                -- Historisation changement de statut
                IF OLD.statut != NEW.statut THEN
                    INSERT INTO historique_commandes (commande_id, action, details)
                    VALUES (NEW.id, "Modification de statut", 
                        CONCAT("Statut changé de ", OLD.statut, " à ", NEW.statut)
                    );
                END IF;
                
                -- Historisation modification de montant
                IF OLD.montant_ttc != NEW.montant_ttc THEN
                    INSERT INTO historique_commandes (commande_id, action, ancienne_valeur, nouvelle_valeur)
                    VALUES (NEW.id, "modification_montant", OLD.montant_ttc, NEW.montant_ttc);
                END IF;
                
                -- Historisation modification de date
                IF OLD.date_livraison_prevue != NEW.date_livraison_prevue THEN
                    INSERT INTO historique_commandes (commande_id, action, ancienne_valeur, nouvelle_valeur, details)
                    VALUES (NEW.id, "modification_date_livraison", 
                            OLD.date_livraison_prevue, 
                            NEW.date_livraison_prevue,
                            "Date de livraison modifiée");
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_historique_commandes');
    }
}