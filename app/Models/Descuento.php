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
        return $this->belongsToMany(
            Alumno::class,
            'alumno_descuento_maestria',
            'descuento_id',
            'alumno_dni'
        )
        ->withPivot('maestria_id')
        ->withTimestamps();
    }
}
