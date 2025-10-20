<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder inclui apenas algumas capitais e cidades importantes como exemplo.
     * Para popular todos os municípios brasileiros, você pode usar a API do IBGE:
     * https://servicodados.ibge.gov.br/api/v1/localidades/municipios
     */
    public function run(): void
    {
        // Principais capitais e cidades
        $municipios = [
            // São Paulo
            ['nome' => 'São Paulo', 'uf' => 'SP', 'ibge_codigo' => 3550308],
            ['nome' => 'Campinas', 'uf' => 'SP', 'ibge_codigo' => 3509502],
            ['nome' => 'Santos', 'uf' => 'SP', 'ibge_codigo' => 3548500],
            ['nome' => 'São José dos Campos', 'uf' => 'SP', 'ibge_codigo' => 3549904],
            ['nome' => 'Ribeirão Preto', 'uf' => 'SP', 'ibge_codigo' => 3543402],
            
            // Rio de Janeiro
            ['nome' => 'Rio de Janeiro', 'uf' => 'RJ', 'ibge_codigo' => 3304557],
            ['nome' => 'Niterói', 'uf' => 'RJ', 'ibge_codigo' => 3303302],
            ['nome' => 'Duque de Caxias', 'uf' => 'RJ', 'ibge_codigo' => 3301702],
            
            // Minas Gerais
            ['nome' => 'Belo Horizonte', 'uf' => 'MG', 'ibge_codigo' => 3106200],
            ['nome' => 'Uberlândia', 'uf' => 'MG', 'ibge_codigo' => 3170206],
            ['nome' => 'Contagem', 'uf' => 'MG', 'ibge_codigo' => 3118601],
            
            // Bahia
            ['nome' => 'Salvador', 'uf' => 'BA', 'ibge_codigo' => 2927408],
            ['nome' => 'Feira de Santana', 'uf' => 'BA', 'ibge_codigo' => 2910800],
            
            // Paraná
            ['nome' => 'Curitiba', 'uf' => 'PR', 'ibge_codigo' => 4106902],
            ['nome' => 'Londrina', 'uf' => 'PR', 'ibge_codigo' => 4113700],
            ['nome' => 'Maringá', 'uf' => 'PR', 'ibge_codigo' => 4115200],
            
            // Rio Grande do Sul
            ['nome' => 'Porto Alegre', 'uf' => 'RS', 'ibge_codigo' => 4314902],
            ['nome' => 'Caxias do Sul', 'uf' => 'RS', 'ibge_codigo' => 4305108],
            ['nome' => 'Pelotas', 'uf' => 'RS', 'ibge_codigo' => 4314407],
            
            // Santa Catarina
            ['nome' => 'Florianópolis', 'uf' => 'SC', 'ibge_codigo' => 4205407],
            ['nome' => 'Joinville', 'uf' => 'SC', 'ibge_codigo' => 4209102],
            ['nome' => 'Blumenau', 'uf' => 'SC', 'ibge_codigo' => 4202404],
            
            // Pernambuco
            ['nome' => 'Recife', 'uf' => 'PE', 'ibge_codigo' => 2611606],
            ['nome' => 'Jaboatão dos Guararapes', 'uf' => 'PE', 'ibge_codigo' => 2607901],
            
            // Ceará
            ['nome' => 'Fortaleza', 'uf' => 'CE', 'ibge_codigo' => 2304400],
            ['nome' => 'Caucaia', 'uf' => 'CE', 'ibge_codigo' => 2303709],
            
            // Goiás
            ['nome' => 'Goiânia', 'uf' => 'GO', 'ibge_codigo' => 5208707],
            ['nome' => 'Aparecida de Goiânia', 'uf' => 'GO', 'ibge_codigo' => 5201405],
            
            // Distrito Federal
            ['nome' => 'Brasília', 'uf' => 'DF', 'ibge_codigo' => 5300108],
            
            // Amazonas
            ['nome' => 'Manaus', 'uf' => 'AM', 'ibge_codigo' => 1302603],
            
            // Pará
            ['nome' => 'Belém', 'uf' => 'PA', 'ibge_codigo' => 1501402],
            ['nome' => 'Ananindeua', 'uf' => 'PA', 'ibge_codigo' => 1500800],
            
            // Maranhão
            ['nome' => 'São Luís', 'uf' => 'MA', 'ibge_codigo' => 2111300],
            
            // Espírito Santo
            ['nome' => 'Vitória', 'uf' => 'ES', 'ibge_codigo' => 3205309],
            ['nome' => 'Vila Velha', 'uf' => 'ES', 'ibge_codigo' => 3205200],
            
            // Mato Grosso
            ['nome' => 'Cuiabá', 'uf' => 'MT', 'ibge_codigo' => 5103403],
            
            // Mato Grosso do Sul
            ['nome' => 'Campo Grande', 'uf' => 'MS', 'ibge_codigo' => 5002704],
            
            // Alagoas
            ['nome' => 'Maceió', 'uf' => 'AL', 'ibge_codigo' => 2704302],
            
            // Sergipe
            ['nome' => 'Aracaju', 'uf' => 'SE', 'ibge_codigo' => 2800308],
            
            // Rondônia
            ['nome' => 'Porto Velho', 'uf' => 'RO', 'ibge_codigo' => 1100205],
            
            // Acre
            ['nome' => 'Rio Branco', 'uf' => 'AC', 'ibge_codigo' => 1200401],
            
            // Roraima
            ['nome' => 'Boa Vista', 'uf' => 'RR', 'ibge_codigo' => 1400100],
            
            // Amapá
            ['nome' => 'Macapá', 'uf' => 'AP', 'ibge_codigo' => 1600303],
            
            // Tocantins
            ['nome' => 'Palmas', 'uf' => 'TO', 'ibge_codigo' => 1721000],
            
            // Piauí
            ['nome' => 'Teresina', 'uf' => 'PI', 'ibge_codigo' => 2211001],
            
            // Rio Grande do Norte
            ['nome' => 'Natal', 'uf' => 'RN', 'ibge_codigo' => 2408102],
            
            // Paraíba
            ['nome' => 'João Pessoa', 'uf' => 'PB', 'ibge_codigo' => 2507507],
        ];

        foreach ($municipios as $municipioData) {
            $estado = Estado::where('uf', $municipioData['uf'])->first();
            
            if ($estado) {
                Municipio::updateOrCreate(
                    ['ibge_codigo' => $municipioData['ibge_codigo']],
                    [
                        'nome' => $municipioData['nome'],
                        'estado_id' => $estado->id,
                        'ibge_codigo' => $municipioData['ibge_codigo'],
                    ]
                );
            }
        }

        $this->command->info('Municípios principais inseridos com sucesso!');
        $this->command->info('Nota: Este seeder inclui apenas capitais e cidades importantes.');
        $this->command->info('Para popular todos os municípios, considere usar a API do IBGE.');
    }
}
