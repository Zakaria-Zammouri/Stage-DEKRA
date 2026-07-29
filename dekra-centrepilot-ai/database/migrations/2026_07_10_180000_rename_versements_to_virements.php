<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renomme la table "versements" en "virements" (nom réel : ordres de virement)
        Schema::rename('versements', 'virements');

        Schema::table('virements', function (Blueprint $table) {
            $table->renameColumn('date_versement', 'date_virement');
        });
    }

    public function down(): void
    {
        Schema::table('virements', function (Blueprint $table) {
            $table->renameColumn('date_virement', 'date_versement');
        });

        Schema::rename('virements', 'versements');
    }
};