<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Events\BeforeExportEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeExport;

class AlumnosExport implements FromCollection, WithHeadings
{
    protected $alumnosMatriculados;

    public function __construct($alumnosMatriculados)
    {
        $this->alumnosMatriculados = $alumnosMatriculados;
    }

    public function collection()
    {
        return $this->alumnosMatriculados->map(function ($alumno) {
            return [
                'Primer Nombre' => $alumno->nombre1,
                'Segundo Nombre' => $alumno->nombre2,
                'Apellido Paterno' => $alumno->apellidop,
                'Apellido Materno' => $alumno->apellidom,
                'Correo Institucional' => $alumno->email_institucional,
                // Agrega más columnas según tus necesidades
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Primer Nombre',
            'Segundo Nombre',
            'Apellido Paterno',
            'Apellido Materno',
            'Correo Institucional',
            // Agrega más columnas según tus necesidades
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->getDelegate()->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
            },
        ];
    }
}