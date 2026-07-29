<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tva_rates', function (Blueprint $table) {
            $table->id();
            $table->string('type_vehicule')->unique();
            $table->decimal('taux', 5, 2);
            $table->timestamps();
        });

        // Taux par défaut (20% = taux standard marocain).
        // ⚠️ Le taux "Moto" est PROVISOIRE en attendant confirmation
        // du chef de centre (cf. cahier des charges, chapitre 6.6).
        DB::table('tva_rates')->insert([
    ['type_vehicule' => 'Moto', 'taux' => 20.00, 'created_at' => now(), 'updated_at' => now()],
]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tva_rates');
    }
};