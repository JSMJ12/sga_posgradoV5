<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'monto',
        'fecha_pago',
        'archivo_comprobante',
        'verificado',
        'modalidad_pago',
        'tipo_pago',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
