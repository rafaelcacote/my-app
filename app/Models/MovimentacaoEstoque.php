<?php

namespace App\Models;

use App\Traits\BelongsToEmpresaThroughLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoEstoque extends Model
{
    use HasFactory, BelongsToEmpresaThroughLoja;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtosestoques.movimentacoes_estoque';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'loja_id',
        'produto_variacao_id',
        'tipo',
        'quantidade',
        'quantidade_anterior',
        'quantidade_atual',
        'motivo',
        'observacao',
        'usuario_id',
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
            'quantidade' => 'integer',
            'quantidade_anterior' => 'integer',
            'quantidade_atual' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Scope para filtrar por tipo de movimentação.
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por loja.
     */
    public function scopeLoja($query, $lojaId)
    {
        return $query->where('loja_id', $lojaId);
    }

    /**
     * Get the loja that belongs to the movimentacao estoque.
     */
    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Get the produto variacao that belongs to the movimentacao estoque.
     */
    public function produtoVariacao()
    {
        return $this->belongsTo(ProdutoVariacao::class);
    }

    /**
     * Get the usuario that belongs to the movimentacao estoque.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
