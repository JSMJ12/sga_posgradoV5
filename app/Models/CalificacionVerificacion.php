<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificacionVerificacion extends Model
{
    use HasFactory;
    protected $table = 'calificacion_verificacion';

    protected $fillable = [
        'docente_dni',
        'asignatura_id',
        'cohorte_id',
        'calificado',
        'editar',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_dni', 'dni');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class, 'cohorte_id');
    }
}
