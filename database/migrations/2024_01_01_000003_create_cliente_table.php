<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plan')->constrained('plan');
            $table->foreignId('id_point')->nullable()->constrained('accespoint');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ip', 15)->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
