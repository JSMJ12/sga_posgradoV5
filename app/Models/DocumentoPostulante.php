<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoPostulante extends Model
{
    use HasFactory;
    protected $table = 'documentos_postulantes';

    protected $fillable = [
        'dni_postulante',
        'tipo_documento',
        'ruta_documento',
        'verificado',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'dni_postulante', 'dni');
    }
}
