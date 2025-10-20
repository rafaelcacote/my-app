<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remover a constraint existente
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_tipo_check;');
        
        // Adicionar nova constraint com valores permitidos
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_tipo_check CHECK (tipo IS NULL OR tipo IN (\'admin\', \'gerente\', \'vendedor\', \'operador\', \'supervisor\', \'assistente\'));');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover a nova constraint
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_tipo_check;');
        
        // Restaurar constraint original (se necessário)
        // DB::statement('ALTER TABLE users ADD CONSTRAINT users_tipo_check CHECK (tipo IS NULL OR tipo IN (\'gerente\', \'vendedor\', \'operador\'));');
    }
};