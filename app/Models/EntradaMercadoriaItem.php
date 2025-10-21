<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntradaMercadoriaItem extends Model
{
    use HasFactory;

    protected $table = 'gestao.entrada_mercadoria_itens';

    protected $fillable = [
        'entrada_mercadoria_id',
        'produto_variacao_id',
        'quantidade',
        'preco_unitario',
        'total',
    ];

    protected $casts = [
        'entrada_mercadoria_id' => 'integer',
        'produto_variacao_id' => 'integer',
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function entradaMercadoria(): BelongsTo
    {
        return $this->belongsTo(EntradaMercadoria::class);
    }

    public function produtoVariacao(): BelongsTo
    {
        return $this->belongsTo(ProdutoVariacao::class);
    }

    // Accessors
    public function getPrecoUnitarioFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_unitario, 2, ',', '.');
    }

    public function getTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }
}
