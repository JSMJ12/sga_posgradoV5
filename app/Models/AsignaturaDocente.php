<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignaturaDocente extends Model
{
    use HasFactory;
    protected $table = 'asignatura_docente';
    protected $fillable = ['asignatura_id', 'docente_dni'];
    
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }
    
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
