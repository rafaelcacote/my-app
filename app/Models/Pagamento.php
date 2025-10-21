<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendasfinanceiro.pagamentos';

    protected $fillable = [
        'venda_id',
        'forma_pagamento',
        'valor',
        'status',
        'data_pagamento',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'status' => 'string',
            'forma_pagamento' => 'string',
            'data_pagamento' => 'datetime',
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

    public function scopePagos($query)
    {
        return $query->where('status', 'pago');
    }

    public function scopeCancelados($query)
    {
        return $query->where('status', 'cancelado');
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_pagamento', [$dataInicio, $dataFim]);
    }

    // Relacionamentos
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    // Accessors
    public function getStatusFormatadoAttribute()
    {
        $statusMap = [
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'cancelado' => 'Cancelado',
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

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    // Métodos
    public function marcarComoPago($dataPagamento = null)
    {
        $this->status = 'pago';
        $this->data_pagamento = $dataPagamento ?? now();
        $this->save();
    }

    public function cancelar()
    {
        $this->status = 'cancelado';
        $this->save();
    }

    public function podeSerPago()
    {
        return $this->status === 'pendente';
    }

    public function podeSerCancelado()
    {
        return $this->status === 'pendente';
    }

    public function isPago()
    {
        return $this->status === 'pago';
    }

    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    public function isCancelado()
    {
        return $this->status === 'cancelado';
    }
}