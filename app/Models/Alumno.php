<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Alumno extends Model
{
    use HasFactory;
    protected $primaryKey = 'dni';
    protected $keyType = 'string';
    protected $fillable = [
        'nombre1',
        'nombre2',
        'apellidop',
        'apellidom',
        'dni',
        'estado_civil',
        'fecha_nacimiento',
        'provincia',
        'canton',
        'barrio',
        'direccion',
        'nacionalidad',
        'etnia',
        'email_personal',
        'email_institucional',
        'carnet_discapacidad',
        'tipo_discapacidad',
        'porcentaje_discapacidad',
        'contra',
        'image',
        'maestria_id',
        'celular',
        'titulo_profesional',
        'universidad_titulo',
        'nacionalidad_indigena',
        'tipo_colegio',
        'cantidad_miembros_hogar',
        'ingreso_total_hogar',
        'nivel_formacion_padre',
        'nivel_formacion_madre',
        'origen_recursos_estudios',
        'pdf_cedula',
        'pdf_papelvotacion',
        'pdf_titulouniversidad',
        'pdf_conadis',
        'pdf_hojavida',
        'carta_aceptacion',
        'pago_matricula',
        'descuento',
        'monto_total',
        'documento',
        'registro',

    ];
    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
    public function maestria()
    {
        return $this->belongsTo(Maestria::class);
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'dni', 'dni');
    }
    public function tesis()
    {
        return $this->hasMany(Tesis::class, 'alumno_dni', 'dni');
    }
    public function retiros()
    {
        return $this->hasMany(Retiro::class, 'alumno_dni');
    }
    public function titulaciones()
    {
        return $this->hasMany(Titulacion::class, 'alumno_dni', 'dni');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alumno) {
            if (empty($alumno->registro)) { // Solo asignar si 'registro' está vacío
                $alumno->registro = self::getNextRegistro();
            }
        });
    }

    private static function getNextRegistro()
    {
        // Obtiene el valor máximo del campo 'registro' en la tabla alumnos
        $lastRegistro = DB::table('alumnos')->max('registro');

        // Incrementa el valor de 'registro'
        return $lastRegistro ? $lastRegistro + 1 : 1;
    }
    public function examenComplexivo()
    {
        return $this->hasOne(ExamenComplexivo::class, 'alumno_dni', 'dni');
    }
    
}
