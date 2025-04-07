<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Cohorte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CohorteAlumnosExport implements FromCollection, WithHeadings
{
    protected $cohorte_id;

    public function __construct($cohorte_id)
    {
        $this->cohorte_id = $cohorte_id;
    }

    public function collection()
    {
        return Alumno::select('nombre1', 'nombre2', 'apellidop', 'apellidom', 'email_institucional', 'celular')
            ->whereIn('dni', function ($query) {
                $query->select('alumno_dni')
                    ->from(with(new Matricula)->getTable())
                    ->where('cohorte_id', $this->cohorte_id);
            })->get();
    }

    public function headings(): array
    {
        return ["Nombre 1", "Nombre 2", "Apellido Paterno", "Apellido Materno", "Email Institucional", "Celular"];
    }
}
