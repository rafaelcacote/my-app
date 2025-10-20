<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtosestoques.produtos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'empresa_id',
        'uuid',
        'sku',
        'nome',
        'descricao',
        'categoria_id',
        'marca_id',
        'preco_custo',
        'preco_venda',
        'margem_lucro',
        'ativo',
        'controle_estoque',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'preco_custo' => 'decimal:2',
            'preco_venda' => 'decimal:2',
            'margem_lucro' => 'decimal:2',
            'ativo' => 'boolean',
            'controle_estoque' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para filtrar apenas produtos ativos.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar apenas produtos inativos.
     */
    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Scope para filtrar produtos com controle de estoque.
     */
    public function scopeComControleEstoque($query)
    {
        return $query->where('controle_estoque', true);
    }

    /**
     * Get the categoria that belongs to the produto.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Get the marca that belongs to the produto.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Get the produto variacoes for the produto.
     */
    public function produtoVariacoes()
    {
        return $this->hasMany(ProdutoVariacao::class);
    }
}
