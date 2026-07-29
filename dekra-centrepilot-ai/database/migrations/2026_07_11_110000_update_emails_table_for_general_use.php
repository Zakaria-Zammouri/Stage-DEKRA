<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->foreignId('titre_propriete_id')->nullable()->change();
            $table->foreignId('document_id')->nullable()
                ->after('titre_propriete_id')
                ->constrained()
                ->nullOnDelete();
            $table->string('type_modele')->default('Libre')->after('objet');
            $table->string('statut_envoi')->default('Envoyé')->after('contenu');
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_id');
            $table->dropColumn(['type_modele', 'statut_envoi']);
        });
    }
};