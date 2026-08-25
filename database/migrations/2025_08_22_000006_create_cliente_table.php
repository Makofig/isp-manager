<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cliente')) {
            return;
        }

        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plan')->constrained('plan')->onDelete('cascade');
            $table->foreignId('id_point')->nullable()->constrained('accespoint')->onDelete('cascade');
            $table->string('nombre', 150);
            $table->string('apellido', 50);
            $table->string('direccion', 255);
            $table->string('telefono', 20)->nullable();
            $table->string('ip', 15)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->boolean('is_banned')->default(0);
            $table->timestamps();

            $table->index('id_plan');
            $table->index('id_point');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
