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
        Schema::create('logs_integration', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('type', ['export', 'import', 'sync']);
            $table->string('entite', 50);
            $table->integer('entite_id')->nullable();
            $table->string('action');
            $table->text('details')->nullable();
            $table->enum('statut', ['success', 'warning', 'error']);
            $table->dateTime('date_log')->useCurrent()->index('idx_logs_integration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_integration');
    }
};
