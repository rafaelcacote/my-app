<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToEmpresa;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

    protected $table = 'vendasfinanceiro.clientes';

    protected $fillable = [
        'empresa_id',
        'uuid',
        'nome',
        'tipo',
        'cpf_cnpj',
        'email',
        'telefone',
        'data_nascimento',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'tipo' => 'string',
            'ativo' => 'boolean',
            'data_nascimento' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Query Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    public function scopeFisicos($query)
    {
        return $query->where('tipo', 'fisica');
    }

    public function scopeJuridicos($query)
    {
        return $query->where('tipo', 'juridica');
    }

    // Relacionamentos
    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    // Accessors
    public function getTipoFormatadoAttribute()
    {
        return $this->tipo === 'fisica' ? 'Pessoa Física' : 'Pessoa Jurídica';
    }

    public function getCpfCnpjFormatadoAttribute()
    {
        if (!$this->cpf_cnpj) return null;
        
        $digits = preg_replace('/\D/', '', $this->cpf_cnpj);
        
        if (strlen($digits) === 11) {
            // CPF: XXX.XXX.XXX-XX
            return substr($digits, 0, 3) . '.' . 
                   substr($digits, 3, 3) . '.' . 
                   substr($digits, 6, 3) . '-' . 
                   substr($digits, 9, 2);
        } elseif (strlen($digits) === 14) {
            // CNPJ: XX.XXX.XXX/XXXX-XX
            return substr($digits, 0, 2) . '.' . 
                   substr($digits, 2, 3) . '.' . 
                   substr($digits, 5, 3) . '/' . 
                   substr($digits, 8, 4) . '-' . 
                   substr($digits, 12, 2);
        }
        
        return $this->cpf_cnpj;
    }

    public function getEnderecoCompletoAttribute()
    {
        $endereco = [];
        
        if ($this->logradouro) {
            $endereco[] = $this->logradouro;
            if ($this->numero) {
                $endereco[] = $this->numero;
            }
        }
        
        if ($this->bairro) {
            $endereco[] = $this->bairro;
        }
        
        if ($this->cidade) {
            $endereco[] = $this->cidade;
            if ($this->estado) {
                $endereco[] = $this->estado;
            }
        }
        
        if ($this->cep) {
            $endereco[] = $this->cep;
        }
        
        return implode(', ', $endereco);
    }
}