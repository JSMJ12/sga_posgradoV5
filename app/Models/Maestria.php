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
        return $this->belongsToMany(Alumno::class, 'alumnos_maestrias', 'maestria_id', 'alumno_dni');
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
        return $this->hasMany(Postulante::class, 'maestria_id', 'id');
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
    public function descuentosAlumnos()
    {
        return $this->belongsToMany(
            Descuento::class,
            'alumno_descuento_maestria',
            'maestria_id',
            'descuento_id'
        )
        ->withPivot('alumno_dni')
        ->withTimestamps();
    }
    public function alumnos_montos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_maestria_monto', 'maestria_id', 'alumno_dni')
                    ->withPivot('monto_arancel', 'monto_matricula', 'monto_inscripcion')
                    ->withTimestamps();
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

}
