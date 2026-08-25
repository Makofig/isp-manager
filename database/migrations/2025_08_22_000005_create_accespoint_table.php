<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accespoint')) {
            return;
        }

        Schema::create('accespoint', function (Blueprint $table) {
            $table->id();
            $table->string('ssid', 150);
            $table->string('frecuencia', 50);
            $table->string('ip_ap', 15);
            $table->string('localidad', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accespoint');
    }
};
