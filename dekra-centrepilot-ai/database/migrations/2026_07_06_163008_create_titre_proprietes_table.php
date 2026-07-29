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
       Schema::create('titre_proprietes', function (Blueprint $table) {
   $table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();

$table->string('nom_proprietaire');
$table->string('numero_immatriculation');
$table->string('marque');
$table->string('modele');

$table->text('description')->nullable();

$table->string('chemin_fichier');

$table->date('date_extraction');

$table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titre_proprietes');
    }
};
