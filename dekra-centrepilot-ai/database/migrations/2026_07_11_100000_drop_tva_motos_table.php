<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tva_motos');
    }

    public function down(): void
    {
        Schema::create('tva_motos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('nombre_motos');
            $table->decimal('taux_tva', 5, 2);
            $table->decimal('tva', 10, 2);
            $table->date('date_calcul');
            $table->timestamps();
        });
    }
};