<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes, BelongsToEmpresa;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtosestoques.categorias';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
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
            'ativo' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para filtrar apenas categorias ativas.
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar apenas categorias inativas.
     */
    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Get the produtos for the categoria.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
