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
        Schema::table('assistant_i_a_s', function (Blueprint $table) {
            // 'question' (comportement existant) ou 'document' (nouvelle fonctionnalité)
            $table->string('type')->default('question')->after('user_id');

            // Rendre question/reponse optionnelles car une analyse de document n'en a pas besoin
            $table->text('question')->nullable()->change();
            $table->text('reponse')->nullable()->change();

            // Champs propres à l'analyse de document
            $table->string('fichier_nom')->nullable()->after('reponse');
            $table->string('type_document')->nullable()->after('fichier_nom');
            $table->json('champs_extraits')->nullable()->after('type_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assistant_i_a_s', function (Blueprint $table) {
            $table->dropColumn(['type', 'fichier_nom', 'type_document', 'champs_extraits']);
            $table->text('question')->nullable(false)->change();
            $table->text('reponse')->nullable(false)->change();
        });
    }
};