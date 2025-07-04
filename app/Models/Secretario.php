<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secretario extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'apellido',
        'contra',
        'sexo',
        'dni',
        'tipo',
        'image',
    ];
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
    public function getFullNameAttribute()
    {
        return trim("{$this->nombre1} {$this->nombre2} {$this->apellidop} {$this->apellidom}");
    }
}
