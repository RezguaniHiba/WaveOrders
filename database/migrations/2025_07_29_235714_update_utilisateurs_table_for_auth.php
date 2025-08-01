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
     Schema::table('utilisateurs', function (Blueprint $table) {
            // Ajoutez toutes les colonnes manquantes
            $table->timestamp('updated_at')->nullable()->after('date_creation');
        });

    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['updated_at']);
        });
    }
};
