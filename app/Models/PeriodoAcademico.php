<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PeriodoAcademico extends Model
{
    use HasFactory;
    protected $table = 'periodos_academicos';
    protected $fillable = [
        'nombre',
        'status',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function isActivo()
    {
        return $this->status === 'ACTIVO';
    }

    public function isVigente()
    {
        return $this->isActivo() && $this->fecha_fin->gte(Carbon::now());
    }
    public function cohortes()
    {
        return $this->hasMany(Cohorte::class);
    }
    public function actualizarEstado()
    {
        $ahora = now();

        if ($this->fecha_inicio > $ahora) {
            $this->status = 'INSCRIPCION';
        } elseif ($this->fecha_fin < $ahora) {
            $this->status = 'INACTIVO';
        } else {
            $this->status = 'ACTIVO';
        }

        $this->save();
    }
}
