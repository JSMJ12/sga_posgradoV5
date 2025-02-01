<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestria extends Model
{
    use HasFactory;
    protected $table = 'maestrias';

    protected $fillable = [
        'codigo', 'nombre', 'status', 'coordinador', 'inscripcion', 'matricula', 'arancel'
    ];
    
    public function cohortes()
    {
        return $this->hasMany(Cohorte::class);
    }
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class);
    }
    public function secciones()
    {
        return $this->belongsToMany(Seccion::class, 'maestria_seccion', 'maestria_id', 'seccion_id');
    }
    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class);
    }
    public function docentes()
    {
        return $this->belongsToMany(Docente::class);
    }
    public function coordinador()
    {
        return $this->belongsTo(Docente::class, 'coordinador');
    }
    public function tasaTitulacion()
    {
        return $this->hasMany(TasaTitulacion::class);
    }
}
