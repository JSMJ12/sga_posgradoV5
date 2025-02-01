<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'piso',
        'codigo',
        'paralelo',
    ];

    public function cohortes()
    {
        return $this->hasMany(Cohorte::class);
    }
}
