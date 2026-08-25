<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pagos')) {
            return;
        }

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')->constrained('cliente')->onDelete('cascade');
            $table->foreignId('id_cuota')->constrained('cuotas')->onDelete('cascade');
            $table->integer('num_cuotas')->nullable();
            $table->decimal('costo', 12, 2);
            $table->decimal('abonado', 12, 2)->default(0);
            $table->decimal('pago_parcial', 10, 2)->default(0);
            $table->boolean('estado')->default(0);
            $table->date('fecha_pago')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->text('comentario')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('image2', 255)->nullable();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
