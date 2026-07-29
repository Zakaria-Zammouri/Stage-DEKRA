<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()
                ->after('document_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('virement_id')->nullable()
                ->after('invoice_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropConstrainedForeignId('virement_id');
        });
    }
};