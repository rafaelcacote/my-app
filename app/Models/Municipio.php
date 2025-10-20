<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'municipios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'estado_id',
        'ibge_codigo',
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
        ];
    }

    /**
     * Get the estado that owns the municipio.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Get all enderecos for this municipio.
     */
    public function enderecos()
    {
        return $this->hasMany(Endereco::class);
    }
}
