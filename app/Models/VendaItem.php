<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'vendasfinanceiro.venda_itens';

    protected $fillable = [
        'venda_id',
        'produto_variacao_id',
        'quantidade',
        'preco_unitario',
        'desconto',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
            'preco_unitario' => 'decimal:2',
            'desconto' => 'decimal:2',
            'total' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // Relacionamentos
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produtoVariacao()
    {
        return $this->belongsTo(ProdutoVariacao::class);
    }

    // Accessors
    public function getPrecoUnitarioFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_unitario, 2, ',', '.');
    }

    public function getDescontoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->desconto, 2, ',', '.');
    }

    public function getTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }

    public function getSubtotalFormatadoAttribute()
    {
        $subtotal = $this->quantidade * $this->preco_unitario;
        return 'R$ ' . number_format($subtotal, 2, ',', '.');
    }

    // Métodos
    public function calcularTotal()
    {
        $subtotal = $this->quantidade * $this->preco_unitario;
        $this->total = $subtotal - $this->desconto;
        $this->save();
        
        // Recalcula o total da venda
        $this->venda->calcularTotal();
    }

    public function getSubtotal()
    {
        return $this->quantidade * $this->preco_unitario;
    }

    public function getDescontoPercentual()
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal == 0) return 0;
        
        return ($this->desconto / $subtotal) * 100;
    }
}