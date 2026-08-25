<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'pago_parcial')) {
                $table->decimal('pago_parcial', 10, 2)->default(0)->after('abonado');
            }
            if (!Schema::hasColumn('pagos', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('fecha_pago');
            }
            if (!Schema::hasIndex('pagos', 'pagos_estado_created_at_index')) {
                $table->index(['estado', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'pago_parcial')) {
                $table->dropColumn('pago_parcial');
            }
            if (Schema::hasColumn('pagos', 'fecha_vencimiento')) {
                $table->dropColumn('fecha_vencimiento');
            }
        });
    }
};
