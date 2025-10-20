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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('endereco', 255)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->onDelete('set null');
            $table->string('cep', 9)->nullable();
            $table->string('referencia', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('cep');
            $table->index('municipio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
