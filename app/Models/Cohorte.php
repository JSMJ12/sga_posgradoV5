<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cohorte extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'maestria_id',
        'periodo_academico_id',
        'aula_id',
        'aforo',
        'modalidad',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function calificacionVerificaciones()
    {
        return $this->hasMany(CalificacionVerificacion::class);
    }
    public function maestria()
    {
        return $this->belongsTo(Maestria::class, 'maestria_id');
    }

    public function periodo_academico()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class,'aula_id');
    }
    public function docentes()
    {
        return $this->belongsToMany(Docente::class, 'cohorte_docente', 'cohort_id', 'docente_dni');
    }
    public function asignaturas()
    {
        return $this->belongsToMany(Asignatura::class, 'cohorte_docente', 'cohort_id', 'asignatura_id');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }
    public function cohorteDocente()
    {
        return $this->hasMany(CohorteDocente::class, 'docente_dni');
    }
    public function tasaTitulacion()
    {
        return $this->hasMany(TasaTitulacion::class);
    }
}
