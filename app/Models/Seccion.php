<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;
    protected $table = 'secciones';
    protected $fillable = ['nombre'];

    public function maestrias()
    {
        return $this->belongsToMany(Maestria::class, 'maestria_seccion', 'seccion_id', 'maestria_id');
    }

    public function secretarios()
    {
        return $this->belongsToMany(Secretario::class);
    }
}
