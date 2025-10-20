<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tamanho extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtosestoques.tamanhos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'empresa_id',
        'nome',
        'tipo',
        'ordem',
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
            'ordem' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para ordenar por ordem.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordem');
    }

    /**
     * Get the produto variacoes for the tamanho.
     */
    public function produtoVariacoes()
    {
        return $this->hasMany(ProdutoVariacao::class);
    }
}
