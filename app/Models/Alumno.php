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
        'ficha_socioeconomica',

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
    ];
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email_institucional');
    }
    public function descuentos()
    {
        return $this->belongsToMany(
            Descuento::class,                 
            'alumno_descuento_maestria',      
            'alumno_dni',                     
            'descuento_id'                     
        )
        ->withPivot('maestria_id')           
        ->withTimestamps();
    }
    public function montos()
    {
        return $this->belongsToMany(Maestria::class, 'alumno_maestria_monto', 'alumno_dni', 'maestria_id')
                    ->withPivot('monto_arancel', 'monto_matricula', 'monto_inscripcion')
                    ->withTimestamps();
    }
    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
    public function maestrias()
    {
        return $this->belongsToMany(Maestria::class, 'alumnos_maestrias', 'alumno_dni', 'maestria_id');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
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

        // Asignar registro automático
        static::creating(function ($alumno) {
            if (empty($alumno->registro)) {
                $alumno->registro = self::getNextRegistro();
            }
        });

        // Cada vez que el alumno se recupera de la BD, verificar titulación
        static::retrieved(function ($alumno) {
            $alumno->verificarYActualizarTitulacion();
        });
    }

    public function verificarYActualizarTitulacion()
    {
        foreach ($this->maestrias as $maestria) {
            $asignaturas = $maestria->asignaturas;
            $notas = $this->notas->whereIn('asignatura_id', $asignaturas->pluck('id'));

            // Verifica si tiene todas las notas considerando recuperación
            if ($asignaturas->count() > 0 && $notas->count() == $asignaturas->count()) {

                $aprobadoTodas = $notas->every(function($nota) {
                    $campos = [
                        'actividades' => $nota->nota_actividades ?? 0,
                        'practicas'   => $nota->nota_practicas ?? 0,
                        'autonomo'    => $nota->nota_autonomo ?? 0,
                        'examen_final'=> $nota->examen_final ?? 0,
                    ];

                    $total = array_sum($campos);

                    if (!is_null($nota->recuperacion) && $nota->recuperacion > 0) {
                        $minKey = array_keys($campos, min($campos))[0];
                        if ($nota->recuperacion > $campos[$minKey]) {
                            $campos[$minKey] = $nota->recuperacion;
                        }
                        $total = array_sum($campos);
                    }

                    return $total >= 7;
                });

                if ($aprobadoTodas) {
                    // Evitar duplicados: solo si el alumno no ha sido contado
                    if (!$this->yaContadoEnTasa($maestria->id)) {

                        // Buscar matrícula de cualquier asignatura de esta maestría
                        $matricula = $this->matriculas()
                            ->whereIn('asignatura_id', $asignaturas->pluck('id'))
                            ->first();

                        if ($matricula) {
                            $cohorteId = $matricula->cohorte_id;
                            $maestriaId = $maestria->id;

                            // ✅ Actualizar o crear registro en TasaTitulacion
                            $tasa = TasaTitulacion::firstOrNew([
                                'cohorte_id' => $cohorteId,
                                'maestria_id' => $maestriaId,
                            ]);

                            $tasa->numero_maestrantes_aprobados = ($tasa->numero_maestrantes_aprobados ?? 0) + 1;
                            $tasa->save();

                            // Registrar en la tabla de control
                            $this->marcarComoAprobadoEnTasa($maestriaId);

                            // Asignar rol al usuario
                            $usuario = User::where('email', $this->email_institucional)->first();
                            if ($usuario && !$usuario->hasRole('Titulado_proceso')) {
                                $usuario->assignRole('Titulado_proceso');
                            }
                        }
                    }
                }
            }
        }
    }


    public function yaContadoEnTasa($maestria_id)
    {
        return AlumnoAprobadoTasa::where('alumno_dni', $this->dni)
            ->where('maestria_id', $maestria_id)
            ->exists();
    }

    public function marcarComoAprobadoEnTasa($maestria_id)
    {
        AlumnoAprobadoTasa::firstOrCreate([
            'alumno_dni' => $this->dni,
            'maestria_id' => $maestria_id
        ]);
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

    public function getFullNameAttribute()
    {
        return "{$this->nombre1} {$this->nombre2} {$this->apellidop} {$this->apellidom}";
    }
}
