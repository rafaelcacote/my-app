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
        $columnExists = DB::selectOne("SELECT 1 FROM information_schema.columns WHERE table_schema = 'multitenancy' AND table_name = 'empresas' AND column_name = 'endereco_id'") !== null;

        if (! $columnExists) {
            Schema::table('multitenancy.empresas', function (Blueprint $table) {
                $table->unsignedBigInteger('endereco_id')->nullable()->after('email');
            });

            // Add FK separately to avoid duplicate constraint issues
            DB::statement("ALTER TABLE multitenancy.empresas ADD CONSTRAINT IF NOT EXISTS empresas_endereco_id_foreign FOREIGN KEY (endereco_id) REFERENCES enderecos(id) ON DELETE SET NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['endereco_id']);
            $table->dropColumn('endereco_id');
        });
    }
};
