<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni', 'monto', 'fecha_pago', 'archivo_comprobante', 'verificado','modalidad_pago',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'dni', 'dni');
    }
}
