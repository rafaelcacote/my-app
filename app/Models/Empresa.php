<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Get the logo URL attribute.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * The connection name for the model.
     *
     * @var string
     */
    //protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multitenancy.empresas';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'email',
        'endereco_id',
        'logo_path',
        'telefone',
        'ativo',
        'data_adesao',
        'data_expiracao',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['logo_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'ativo' => 'boolean',
            'data_adesao' => 'datetime',
            'data_expiracao' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope para filtrar apenas empresas ativas.
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar apenas empresas inativas.
     */
    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Get the endereco that belongs to the empresa.
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Get the lojas for the empresa.
     */
    public function lojas()
    {
        return $this->hasMany(Loja::class);
    }
}
