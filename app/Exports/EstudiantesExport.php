<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EstudiantesExport implements FromView
{
    protected $alumnos, $maestria, $cohorte;

    public function __construct($alumnos, $maestria, $cohorte)
    {
        $this->alumnos = $alumnos;
        $this->maestria = $maestria;
        $this->cohorte = $cohorte;
    }

    public function view(): View
    {
        return view('exports.estudiantes', [
            'alumnos' => $this->alumnos,
            'maestria' => $this->maestria,
            'cohorte' => $this->cohorte,
        ]);
    }
}
