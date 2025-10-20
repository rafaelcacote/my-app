<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar schema multitenancy se não existir
        DB::statement('CREATE SCHEMA IF NOT EXISTS multitenancy');

        // Criar tabela empresas no schema multitenancy
        if (!Schema::hasTable('multitenancy.empresas')) {
            Schema::create('multitenancy.empresas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('gen_random_uuid()'));
            $table->string('razao_social', 255);
            $table->string('nome_fantasia', 255);
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('email', 255);
            $table->string('telefone', 20)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestampTz('data_adesao')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('data_expiracao')->nullable();
            $table->timestampsTz(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
            
            // Índices para performance
            $table->index('ativo');
            $table->index('cnpj');
            $table->index('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multitenancy.empresas');
    }
};
