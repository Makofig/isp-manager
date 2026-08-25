<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 200);
            $table->enum('categoria', [
                'cables_utp',
                'herramientas',
                'rj45',
                'routers_clientes',
                'equipos_nodos',
                'fibra_optica',
                'antenas',
                'postes_torres',
                'combustible',
                'salarios',
                'alquiler',
                'servicios',
                'reparaciones',
                'otros'
            ]);
            $table->decimal('monto', 12, 2);
            $table->date('fecha_gasto');
            $table->string('proveedor', 150)->nullable();
            $table->string('comprobante', 255)->nullable();
            $table->text('notas')->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['categoria', 'fecha_gasto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
