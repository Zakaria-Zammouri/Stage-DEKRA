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
        Schema::create('invoices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('numero')->unique();

            $table->string('client');

            $table->string('objet');

            $table->decimal('montant_ht', 10, 2);

            $table->decimal('tva', 10, 2);

            $table->decimal('montant_ttc', 10, 2);

            $table->date('date_facture');

            $table->enum('statut', [
                'Payée',
                'Non payée'
            ])->default('Non payée');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};