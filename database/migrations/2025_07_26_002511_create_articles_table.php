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
        Schema::create('articles', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code_wavesoft', 20)->unique('code_wavesoft');
            $table->string('reference', 50)->unique('reference');
            $table->string('designation');
            $table->text('description')->nullable();
            $table->decimal('prix_ht', 10);
            $table->decimal('taux_tva', 5)->default(20);
            $table->string('unite', 10)->nullable()->default('pièce');
            $table->integer('stock_disponible')->nullable()->default(0);
            $table->integer('stock_reserve')->nullable()->default(0);
            $table->dateTime('date_maj_stock')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('famille_id')->nullable()->index('famille_id');

            $table->index(['code_wavesoft'], 'idx_articles_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
