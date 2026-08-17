<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accespoint', function (Blueprint $table) {
            $table->id();
            $table->string('ssid')->nullable();
            $table->string('frecuencia')->nullable();
            $table->string('ip_ap', 15)->nullable();
            $table->string('localidad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accespoint');
    }
};
