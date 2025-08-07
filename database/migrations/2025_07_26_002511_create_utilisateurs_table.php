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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nom', 100);
            $table->string('email', 100)->unique('email');
            $table->string('telephone', 20)->nullable();
            $table->text('mot_de_passe_hash');
            $table->enum('role', ['admin', 'commercial'])->default('commercial');
            $table->boolean('actif')->default(true);
            $table->dateTime('date_creation')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
