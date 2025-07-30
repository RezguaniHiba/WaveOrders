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
        Schema::create('transferts_wavesoft', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('commande_id')->index('idx_transfert_commande');
            $table->dateTime('date_export')->nullable();
            $table->string('fichier_genere')->nullable();
            $table->string('etat_traitement', 20)->nullable()->default('en_attente');
            $table->text('message_retour')->nullable();
            $table->string('wavesoft_code_objet', 50)->nullable();
            $table->char('wavesoft_etat', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferts_wavesoft');
    }
};
