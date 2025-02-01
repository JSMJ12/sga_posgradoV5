<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulacion extends Model
{
    use HasFactory;

    // Definir el nombre de la tabla
    protected $table = 'titulaciones'; 

    // Definir los campos que se pueden llenar masivamente
    protected $fillable = [
        'alumno_dni',
        'titulado',
        'tesis_path',
        'fecha_graduacion'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_dni', 'dni');
    }
}
