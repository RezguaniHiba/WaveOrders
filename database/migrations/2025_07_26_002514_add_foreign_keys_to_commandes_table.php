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
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreign(['client_id'], 'commandes_ibfk_1')->references(['id'])->on('clients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['commercial_id'], 'commandes_ibfk_2')->references(['id'])->on('utilisateurs')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['cree_par'], 'commandes_ibfk_3')->references(['id'])->on('utilisateurs')->onUpdate('no action')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign('commandes_ibfk_1');
            $table->dropForeign('commandes_ibfk_2');
            $table->dropForeign('commandes_ibfk_3');
        });
    }
};
