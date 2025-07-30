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
        Schema::create('familles_articles', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code_wavesoft', 20)->nullable();
            $table->string('libelle', 100);
            $table->integer('parent_id')->nullable()->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('familles_articles');
    }
};
