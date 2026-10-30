<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a `year` column to cuotas and changes the unique constraint
     * from `numero` alone to `(numero, year)` so quotas can be generated
     * for future years without violating uniqueness.
     */
    public function up(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->integer('year')->default(now()->year)->after('numero');

            // Drop the existing unique constraint on 'numero'
            if (Schema::hasIndex('cuotas', 'cuotas_numero_unique')) {
                $table->dropUnique('cuotas_numero_unique');
            }

            // Add composite unique on (numero, year)
            $table->unique(['numero', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropUnique(['numero', 'year']);
            $table->unique('numero');
            $table->dropColumn('year');
        });
    }
};
