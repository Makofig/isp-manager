<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')->constrained('cliente');
            $table->foreignId('id_cuota')->constrained('cuotas');
            $table->integer('num_cuotas')->default(1);
            $table->decimal('costo', 10, 2)->default(0);
            $table->decimal('abonado', 10, 2)->default(0);
            $table->boolean('estado')->default(false);
            $table->timestamp('fecha_pago')->nullable();
            $table->string('comentario')->nullable();
            $table->string('image')->nullable();
            $table->string('image2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
