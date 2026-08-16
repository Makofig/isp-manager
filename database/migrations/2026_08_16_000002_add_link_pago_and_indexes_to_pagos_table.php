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
        Schema::table('pagos', function (Blueprint $table) {
            // Add link_pago column for MercadoPago payment links
            $table->string('link_pago', 500)->nullable()->after('comentario');

            // Add indexes for common query patterns
            $table->index('id_cliente');
            $table->index('id_cuota');
            $table->index('estado');
        });

        Schema::table('cliente', function (Blueprint $table) {
            // Add index for banned client filtering
            $table->index('is_banned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('link_pago');
            $table->dropIndex(['id_cliente']);
            $table->dropIndex(['id_cuota']);
            $table->dropIndex(['estado']);
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->dropIndex(['is_banned']);
        });
    }
};
