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
        Schema::create('clients', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code_wavesoft', 20)->nullable()->unique('code_wavesoft');
            $table->string('nom', 100);
            $table->string('email', 100)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville', 50)->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('pays', 50)->nullable()->default('France');
            $table->dateTime('date_creation')->useCurrent();
            $table->dateTime('date_maj')->useCurrentOnUpdate()->nullable();
            $table->integer('commercial_id')->nullable()->index('commercial_id');

            $table->index(['code_wavesoft'], 'idx_clients_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
