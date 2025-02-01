<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TasaTitulacion extends Model
{
    use HasFactory;
    protected $table = 'tasa_titulacion';
    protected $fillable = [
        'cohorte_id',
        'maestria_id',
        'numero_matriculados',
        'numero_maestrantes_aprobados',
        'retirados',
        'graduados',
        'no_graduados',
        'examen_complexivo',
        'estado',
    ];

    
    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class);
    }

    public function maestria()
    {
        return $this->belongsTo(Maestria::class);
    }
}
