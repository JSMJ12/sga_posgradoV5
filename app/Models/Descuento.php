<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $fillable = [
        'nombre',
        'porcentaje',
        'activo',
        'requisitos',
        'comprobante',
    ];
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
}
