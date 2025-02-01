<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    use HasFactory;

    protected $table = 'tesis';

    protected $fillable = [
        'alumno_dni',
        'tutor_dni',
        'tema',
        'descripcion',
        'solicitud_pdf',
        'estado',
        'tipo',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_dni', 'dni');
    }

    public function tutor()
    {
        return $this->belongsTo(Docente::class, 'tutor_dni', 'dni');
    }
    public function tutorias()
    {
        return $this->hasMany(Tutoria::class);
    }
}
