<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virements', function (Blueprint $table) {
            $table->enum('statut', ['En attente', 'Traité'])
                ->default('En attente')
                ->after('motif');
        });
    }

    public function down(): void
    {
        Schema::table('virements', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};