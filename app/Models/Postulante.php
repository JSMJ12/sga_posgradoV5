<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Postulante extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'postulantes';
    protected $primaryKey = 'dni';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        // Datos personales
        'dni',
        'apellidop',
        'apellidom',
        'nombre1',
        'nombre2',
        'correo_electronico',
        'celular',
        'telefono_convencional',
        'fecha_nacimiento',
        'edad',
        'sexo',
        'tipo_sangre',
        'nacionalidad',
        'anios_residencia',
        'libreta_militar',
        'discapacidad',
        'tipo_discapacidad',
        'porcentaje_discapacidad',
        'codigo_conadis',
        'numero_matricula',
        'imagen',

        // Lugar de residencia
        'pais_residencia',
        'provincia',
        'canton',
        'parroquia',
        'calle_principal',
        'numero_direccion',
        'calle_secundaria',
        'referencia_direccion',
        'telefono_domicilio',
        'celular_residencia',

        // Contacto de emergencia
        'contacto_apellidos',
        'contacto_nombres',
        'contacto_parentesco',
        'contacto_telefono',
        'contacto_celular',

        // Académica
        'especialidad_bachillerato',
        'colegio_bachillerato',
        'ciudad_bachillerato',
        'titulo_profesional',
        'especialidad_mencion',
        'universidad_titulo',
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

        // Datos socioeconómicos
        'etnia',
        'nacionalidad_indigena',
        'direccion',
        'tipo_colegio',
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
        'carta_aceptacion',

        // Estado y relación
        'maestria_id',
        'status',
        'pago_matricula',
        'monto_matricula',
        'monto_inscripcion',
    ];

    public function maestria()
    {
        return $this->belongsTo(Maestria::class, 'maestria_id');
    }

    public function documentos_verificados()
    {
        return $this->hasMany(DocumentoPostulante::class, 'dni_postulante', 'dni');
    }
}
