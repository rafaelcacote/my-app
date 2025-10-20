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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->foreignId('estado_id')->constrained('estados')->onDelete('cascade');
            $table->integer('ibge_codigo')->unique();
            $table->timestamps();
            
            // Índices
            $table->index('nome');
            $table->index('estado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
