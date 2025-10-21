<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToEmpresa;

class Venda extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

    protected $table = 'vendasfinanceiro.vendas';

    protected $fillable = [
        'empresa_id',
        'loja_id',
        'uuid',
        'cliente_id',
        'numero_venda',
        'status',
        'subtotal',
        'desconto',
        'total',
        'forma_pagamento',
        'observacoes',
        'usuario_id',
        'data_venda',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'status' => 'string',
            'subtotal' => 'decimal:2',
            'desconto' => 'decimal:2',
            'total' => 'decimal:2',
            'forma_pagamento' => 'string',
            'data_venda' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Query Scopes
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_venda', [$dataInicio, $dataFim]);
    }

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    // Accessors
    public function getStatusFormatadoAttribute()
    {
        $statusMap = [
            'pendente' => 'Pendente',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    public function getFormaPagamentoFormatadaAttribute()
    {
        $formasMap = [
            'dinheiro' => 'Dinheiro',
            'cartao_debito' => 'Cartão de Débito',
            'cartao_credito' => 'Cartão de Crédito',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            'transferencia' => 'Transferência',
        ];

        return $formasMap[$this->forma_pagamento] ?? $this->forma_pagamento;
    }

    public function getTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }

    public function getSubtotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getDescontoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->desconto, 2, ',', '.');
    }

    // Métodos
    public function calcularTotal()
    {
        $subtotal = $this->itens->sum(function ($item) {
            return $item->quantidade * $item->preco_unitario;
        });

        $this->subtotal = $subtotal;
        $this->total = $subtotal - $this->desconto;
        $this->save();
    }

    public function adicionarItem($produtoVariacaoId, $quantidade, $precoUnitario, $desconto = 0)
    {
        $total = ($quantidade * $precoUnitario) - $desconto;

        return $this->itens()->create([
            'produto_variacao_id' => $produtoVariacaoId,
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
            'desconto' => $desconto,
            'total' => $total,
        ]);
    }

    public function podeSerCancelada()
    {
        return $this->status === 'pendente';
    }

    public function podeSerConcluida()
    {
        return $this->status === 'pendente' && $this->itens->count() > 0;
    }
}