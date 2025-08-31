<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulacion extends Model
{
    use HasFactory;

    protected $table = 'titulaciones'; 

    protected $fillable = [
        'tesis_id',
        'titulado',
        'tesis_path',
        'fecha_graduacion'
    ];

   public function tesis()
    {
        return $this->belongsTo(Tesis::class);
    }
}
