<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Endereco extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enderecos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'municipio_id',
        'cep',
        'referencia',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the municipio that owns the endereco.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Get all empresas that use this endereco.
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'endereco_id');
    }
}

