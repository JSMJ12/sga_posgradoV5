<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohorteDocente extends Model
{
    use HasFactory;
    protected $table = 'cohorte_docente';
    protected $fillable = ['cohort_id', 'docente_dni', 'asignatura_id', 'calificado',
    'editar',];
    
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class);
    }
}
