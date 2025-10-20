<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nome' => 'Acre', 'uf' => 'AC', 'ibge_codigo' => 12],
            ['nome' => 'Alagoas', 'uf' => 'AL', 'ibge_codigo' => 27],
            ['nome' => 'Amapá', 'uf' => 'AP', 'ibge_codigo' => 16],
            ['nome' => 'Amazonas', 'uf' => 'AM', 'ibge_codigo' => 13],
            ['nome' => 'Bahia', 'uf' => 'BA', 'ibge_codigo' => 29],
            ['nome' => 'Ceará', 'uf' => 'CE', 'ibge_codigo' => 23],
            ['nome' => 'Distrito Federal', 'uf' => 'DF', 'ibge_codigo' => 53],
            ['nome' => 'Espírito Santo', 'uf' => 'ES', 'ibge_codigo' => 32],
            ['nome' => 'Goiás', 'uf' => 'GO', 'ibge_codigo' => 52],
            ['nome' => 'Maranhão', 'uf' => 'MA', 'ibge_codigo' => 21],
            ['nome' => 'Mato Grosso', 'uf' => 'MT', 'ibge_codigo' => 51],
            ['nome' => 'Mato Grosso do Sul', 'uf' => 'MS', 'ibge_codigo' => 50],
            ['nome' => 'Minas Gerais', 'uf' => 'MG', 'ibge_codigo' => 31],
            ['nome' => 'Pará', 'uf' => 'PA', 'ibge_codigo' => 15],
            ['nome' => 'Paraíba', 'uf' => 'PB', 'ibge_codigo' => 25],
            ['nome' => 'Paraná', 'uf' => 'PR', 'ibge_codigo' => 41],
            ['nome' => 'Pernambuco', 'uf' => 'PE', 'ibge_codigo' => 26],
            ['nome' => 'Piauí', 'uf' => 'PI', 'ibge_codigo' => 22],
            ['nome' => 'Rio de Janeiro', 'uf' => 'RJ', 'ibge_codigo' => 33],
            ['nome' => 'Rio Grande do Norte', 'uf' => 'RN', 'ibge_codigo' => 24],
            ['nome' => 'Rio Grande do Sul', 'uf' => 'RS', 'ibge_codigo' => 43],
            ['nome' => 'Rondônia', 'uf' => 'RO', 'ibge_codigo' => 11],
            ['nome' => 'Roraima', 'uf' => 'RR', 'ibge_codigo' => 14],
            ['nome' => 'Santa Catarina', 'uf' => 'SC', 'ibge_codigo' => 42],
            ['nome' => 'São Paulo', 'uf' => 'SP', 'ibge_codigo' => 35],
            ['nome' => 'Sergipe', 'uf' => 'SE', 'ibge_codigo' => 28],
            ['nome' => 'Tocantins', 'uf' => 'TO', 'ibge_codigo' => 17],
        ];

        foreach ($estados as $estado) {
            Estado::updateOrCreate(
                ['uf' => $estado['uf']],
                $estado
            );
        }

        $this->command->info('Estados brasileiros inseridos com sucesso!');
    }
}
