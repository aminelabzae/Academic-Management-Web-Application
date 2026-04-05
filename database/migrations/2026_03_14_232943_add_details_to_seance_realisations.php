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
        Schema::table('seance_realisations', function (Blueprint $table) {
            $table->unsignedBigInteger('professeur_id')->nullable()->after('emploi_du_temps_id');
            $table->unsignedBigInteger('groupe_id')->nullable()->after('professeur_id');
            $table->integer('duree_minutes')->default(0)->after('date');
            
            $table->foreign('professeur_id')->references('id')->on('professeurs')->onDelete('set null');
            $table->foreign('groupe_id')->references('id')->on('groupes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seance_realisations', function (Blueprint $table) {
            $table->dropForeign(['professeur_id']);
            $table->dropForeign(['groupe_id']);
            $table->dropColumn(['professeur_id', 'groupe_id', 'duree_minutes']);
        });
    }
};
