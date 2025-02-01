<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutoria extends Model
{
    use HasFactory;

    protected $table = 'tutorias';

    protected $fillable = [
        'tesis_id',
        'tutor_dni',
        'fecha',
        'observaciones',
        'estado',
        'tipo',
        'link_reunion',
        'lugar'
    ];

    // Relación con Tesis
    public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }

    // Relación con Docente (Tutor)
    public function tutor()
    {
        return $this->belongsTo(Docente::class, 'tutor_dni', 'dni');
    }
}
