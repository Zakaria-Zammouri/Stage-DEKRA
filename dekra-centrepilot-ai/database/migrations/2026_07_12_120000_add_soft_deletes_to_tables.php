<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('virements', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('versements', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('rappels', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('virements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('versements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('rappels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};