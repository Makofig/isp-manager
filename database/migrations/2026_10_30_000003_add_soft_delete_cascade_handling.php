<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * When a client is soft-deleted, related payments should remain
     * accessible. The foreign key on pagos.id_cliente is changed to
     * SET NULL so orphaned payment records are preserved but unlinked.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cliente')->nullable()->change();

            $table->dropForeign(['id_cliente']);
            $table->foreign('id_cliente')
                ->references('id')
                ->on('cliente')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['id_cliente']);
            $table->foreign('id_cliente')
                ->references('id')
                ->on('cliente');

            $table->unsignedBigInteger('id_cliente')->nullable(false)->change();
        });
    }
};
