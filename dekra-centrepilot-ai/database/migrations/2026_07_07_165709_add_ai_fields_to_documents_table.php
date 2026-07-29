<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            $table->string('nom_proprietaire')->nullable()->after('texte_ocr');
            $table->string('immatriculation')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            $table->dropColumn([
                'nom_proprietaire',
                'immatriculation',
                'marque',
                'modele'
            ]);

        });
    }
};