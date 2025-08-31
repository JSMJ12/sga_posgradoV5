<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoAprobadoTasa extends Model
{
    use HasFactory;

    protected $table = 'alumnos_aprobados_tasa';
    protected $fillable = ['alumno_dni', 'maestria_id'];
}
