<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versements', function (Blueprint $table) {
            $table->id();
           $table->unsignedBigInteger('user_id');

$table->foreign('user_id', 'versements_new_user_fk')
      ->references('id')
      ->on('users')
      ->cascadeOnDelete();
            $table->string('reference')->nullable();      // n° chèque, référence bancaire, etc.
            $table->string('client')->nullable();          // qui a payé
            $table->decimal('montant', 10, 2);
            $table->date('date_versement');
            $table->enum('mode_reglement', ['Espèces', 'Chèque', 'Virement', 'Carte bancaire'])
                ->default('Espèces');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versements');
    }
};