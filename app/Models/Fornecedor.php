<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'gestao.fornecedores';
    
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    protected $fillable = [
        'empresa_id',
        'uuid',
        'nome',
        'cpf_cnpj',
        'email',
        'telefone',
        'endereco_id',
        'ativo',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'endereco_id' => 'integer',
        'uuid' => 'string',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    public function entradasMercadoria(): HasMany
    {
        return $this->hasMany(EntradaMercadoria::class);
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

    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
