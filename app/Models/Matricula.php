<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;
    protected $fillable = [
        'alumno_dni',
        'asignatura_id',
        'cohorte_id',
        'docente_dni',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
