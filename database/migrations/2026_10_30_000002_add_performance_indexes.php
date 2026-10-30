<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds indexes on frequently queried columns for better
     * performance on statistics, filtering, and relationship lookups.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('estado');
            $table->index('fecha_pago');
            $table->index('id_cliente');
            $table->index(['id_cuota', 'estado']);
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->index('id_plan');
            $table->index('id_point');
            $table->index('is_banned');
        });

        Schema::table('cuotas', function (Blueprint $table) {
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_pago']);
            $table->dropIndex(['id_cliente']);
            $table->dropIndex(['id_cuota', 'estado']);
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->dropIndex(['id_plan']);
            $table->dropIndex(['id_point']);
            $table->dropIndex(['is_banned']);
        });

        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropIndex(['year']);
        });
    }
};
