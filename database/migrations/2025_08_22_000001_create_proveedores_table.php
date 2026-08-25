<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('contacto', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();

            $table->integer('mb_up')->default(0);
            $table->integer('mb_down')->default(0);

            $table->decimal('precio_total', 12, 2)->default(0);
            $table->decimal('precio_por_mb', 10, 4)->default(0);

            $table->enum('tipo', ['internet', 'equipamiento', 'ambos'])->default('internet');

            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
