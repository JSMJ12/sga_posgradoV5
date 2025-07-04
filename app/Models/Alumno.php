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
        // Datos personales
        'dni',
        'nombre1',
        'nombre2',
        'apellidop',
        'apellidom',
        'sexo',
        'estado_civil',
        'fecha_nacimiento',
        'edad',
        'tipo_sangre',
        'nacionalidad',
        'etnia',
        'nacionalidad_indigena',
        'anios_residencia',
        'libreta_militar',
        'numero_matricula',

        // Contacto
        'email_personal',
        'email_institucional',
        'correo_electronico', // Alias usado en algunas vistas
        'telefono_convencional',
        'celular',
        'telefono_domicilio',
        'celular_residencia',

        // Residencia
        'provincia',
        'canton',
        'parroquia',
        'barrio',
        'direccion',
        'pais_residencia',
        'calle_principal',
        'numero_direccion',
        'calle_secundaria',
        'referencia_direccion',

        // Discapacidad
        'discapacidad',
        'tipo_discapacidad',
        'porcentaje_discapacidad',
        'codigo_conadis',
        'carnet_discapacidad',

        // Emergencia
        'contacto_apellidos',
        'contacto_nombres',
        'contacto_parentesco',
        'contacto_telefono',
        'contacto_celular',

        // Académico
        'tipo_colegio',
        'especialidad_bachillerato',
        'colegio_bachillerato',
        'ciudad_bachillerato',
        'titulo_profesional',
        'universidad_titulo',
        'especialidad_mencion',
        'ciudad_universidad',
        'pais_universidad',
        'registro_senescyt',
        'titulo_posgrado',
        'denominacion_posgrado',
        'universidad_posgrado',
        'ciudad_posgrado',
        'pais_posgrado',

        // Laboral
        'lugar_trabajo',
        'funcion_laboral',
        'ciudad_trabajo',
        'direccion_trabajo',
        'telefono_trabajo',

        // Económico
        'cantidad_miembros_hogar',
        'ingreso_total_hogar',
        'nivel_formacion_padre',
        'nivel_formacion_madre',
        'origen_recursos_estudios',

        // Documentos
        'pdf_cedula',
        'pdf_papelvotacion',
        'pdf_titulouniversidad',
        'pdf_conadis',
        'pdf_hojavida',
        'documento',
        'registro',

        // Otros
        'contra',
        'image',
        'status',
        'carta_aceptacion',
        'pago_matricula',
        'monto_total',
        'monto_matricula',
        'monto_inscripcion',
        'maestria_id',
        'descuento_id'
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
            if (empty($alumno->registro)) {
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

    public function descuento()
    {
        return $this->belongsTo(Descuento::class);
    }
    public function getFullNameAttribute()
    {
        return "{$this->nombre1} {$this->nombre2} {$this->apellidop} {$this->apellidom}";
    }
}
