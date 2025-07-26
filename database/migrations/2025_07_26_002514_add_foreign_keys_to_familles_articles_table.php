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
        Schema::table('familles_articles', function (Blueprint $table) {
            $table->foreign(['parent_id'], 'familles_articles_ibfk_1')->references(['id'])->on('familles_articles')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familles_articles', function (Blueprint $table) {
            $table->dropForeign('familles_articles_ibfk_1');
        });
    }
};
