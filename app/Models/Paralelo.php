<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    use HasFactory;
    protected $table = 'paralelos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];
    public $timestamps = false;

    public function aulas()
    {
        return $this->hasMany(Aula::class, 'paralelos_id');
    }
}
