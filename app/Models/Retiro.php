<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    use HasFactory;

    protected $table = 'retiros';

    // Campos asignables en masa
    protected $fillable = [
        'alumno_dni',
        'documento_path',
        'fecha_retiro',
    ];

    /**
     * Relación: Un retiro pertenece a un alumno.
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_dni');
    }
}
