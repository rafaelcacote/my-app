<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->constrained('multitenancy.empresas')->onDelete('set null');
            $table->string('tipo', 20)->nullable();
            $table->boolean('ativo')->default(true);
            
            // Índices para performance
            $table->index('empresa_id');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropIndex(['empresa_id']);
            $table->dropIndex(['ativo']);
            $table->dropColumn(['empresa_id', 'tipo', 'ativo']);
        });
    }
};
