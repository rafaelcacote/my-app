<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntradaMercadoria extends Model
{
    use HasFactory;

    protected $table = 'gestao.entradas_mercadoria';

    protected $fillable = [
        'empresa_id',
        'loja_id',
        'fornecedor_id',
        'numero_nota',
        'data_entrada',
        'valor_total',
        'observacoes',
        'usuario_id',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'loja_id' => 'integer',
        'fornecedor_id' => 'integer',
        'data_entrada' => 'datetime',
        'valor_total' => 'decimal:2',
        'usuario_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(EntradaMercadoriaItem::class, 'entrada_mercadoria_id');
    }

    // Query Scopes
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeDaLoja($query, $lojaId)
    {
        return $query->where('loja_id', $lojaId);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_entrada', [$dataInicio, $dataFim]);
    }

    public function scopePorFornecedor($query, $fornecedorId)
    {
        return $query->where('fornecedor_id', $fornecedorId);
    }

    // Accessors
    public function getDataEntradaFormatadaAttribute()
    {
        return $this->data_entrada ? $this->data_entrada->format('d/m/Y H:i') : null;
    }

    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }
}
