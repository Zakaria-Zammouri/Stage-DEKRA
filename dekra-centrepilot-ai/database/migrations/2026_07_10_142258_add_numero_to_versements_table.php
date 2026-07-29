<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('versements', function (Blueprint $table) {
            $table->string('numero')->unique()->after('montant_lettres');
        });
    }

    public function down(): void
    {
        Schema::table('versements', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};