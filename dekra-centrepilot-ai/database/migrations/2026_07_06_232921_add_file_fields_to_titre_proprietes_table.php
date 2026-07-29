<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titre_proprietes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('modele');
            $table->string('chemin_fichier')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('titre_proprietes', function (Blueprint $table) {
            $table->dropColumn(['description', 'chemin_fichier']);
        });
    }
};
