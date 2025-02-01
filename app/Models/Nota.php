<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    protected $fillable = [
        'nota_actividades',
        'nota_practicas',
        'nota_autonomo',
        'examen_final',
        'recuperacion',
        'total',
        'cohorte_id',
        'asignatura_id',
        'docente_dni',
        'alumno_dni',
    ];

    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class);
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
