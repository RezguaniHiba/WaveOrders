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
        Schema::create('sync_wavesoft', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('entite_type', ['commande', 'client', 'article', 'stock']);
            $table->integer('entite_id');
            $table->enum('action', ['create', 'update', 'delete']);
            $table->enum('statut', ['pending', 'processing', 'completed', 'failed'])->default('pending')->index('idx_sync_statut');
            $table->dateTime('date_creation')->useCurrent();
            $table->dateTime('date_traitement')->nullable();
            $table->text('erreur')->nullable();
            $table->integer('tentatives')->default(0);

            $table->index(['statut', 'date_creation'], 'idx_sync_wavesoft_pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_wavesoft');
    }
};
