<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenComplexivo extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'examen_complexivo';

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array
     */
    protected $fillable = [
        'nota',
        'lugar',
        'fecha_hora',
        'alumno_dni',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_dni', 'dni');
    }
}
