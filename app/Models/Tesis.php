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
        'maestria_id',
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
    public function maestria()
    {
        return $this->belongsTo(Maestria::class, 'maestria_id');
    }
   public function titulaciones()
    {
        return $this->hasMany(Titulacion::class, 'tesis_id'); 
    }
}
