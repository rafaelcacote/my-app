<?php

namespace App\Models;

use App\Traits\BelongsToEmpresaThroughProduto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoVariacao extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresaThroughProduto;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtosestoques.produto_variacoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'produto_id',
        'tamanho_id',
        'cor_id',
        'sku_variacao',
        'preco_adicional',
        'ativo',
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
            'preco_adicional' => 'decimal:2',
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para filtrar apenas variações ativas.
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar apenas variações inativas.
     */
    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Get the produto that belongs to the produto variacao.
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Get the tamanho that belongs to the produto variacao.
     */
    public function tamanho()
    {
        return $this->belongsTo(Tamanho::class);
    }

    /**
     * Get the cor that belongs to the produto variacao.
     */
    public function cor()
    {
        return $this->belongsTo(Cor::class);
    }

    /**
     * Get the movimentacoes estoque for the produto variacao.
     */
    public function movimentacoesEstoque()
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }
}
