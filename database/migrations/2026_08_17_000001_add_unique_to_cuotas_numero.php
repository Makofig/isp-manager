<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a unique constraint to cuotas.numero to enforce
     * the business rule: only ONE quota per month.
     */
    public function up(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->unique('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropUnique('cuotas_numero_unique');
        });
    }
};
