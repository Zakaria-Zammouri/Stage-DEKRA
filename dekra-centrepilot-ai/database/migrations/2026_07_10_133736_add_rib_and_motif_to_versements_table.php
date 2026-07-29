<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('versements', function (Blueprint $table) {

            $table->string('rib_beneficiaire')->nullable()->after('beneficiaire');

            $table->string('motif')->nullable()->after('montant_lettres');

        });
    }

    public function down(): void
    {
        Schema::table('versements', function (Blueprint $table) {

            $table->dropColumn([
                'rib_beneficiaire',
                'motif'
            ]);

        });
    }
};