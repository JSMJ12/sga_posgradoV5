<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Cohorte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CohorteAlumnosExport implements FromCollection, WithMapping, WithStyles, WithEvents, WithDrawings, WithStartRow, WithCustomStartCell
{
    protected $cohorte_id;
    protected $maestria_nombre;

    public function startCell(): string
    {
        return 'A9';
    }


    public function __construct($cohorte_id)
    {
        $this->cohorte_id = $cohorte_id;
        $cohorte = Cohorte::with('maestria')->findOrFail($cohorte_id);
        $this->maestria_nombre = strtoupper($cohorte->maestria->nombre);
    }

    public function collection()
    {
        return Alumno::select(
            'nombre1',
            'nombre2',
            'apellidop',
            'apellidom',
            'email_institucional',
            'email_personal',
            'celular'
        )
            ->whereIn('dni', function ($query) {
                $query->select('alumno_dni')
                    ->from((new Matricula)->getTable())
                    ->where('cohorte_id', $this->cohorte_id);
            })
            ->get()
            ->sortBy(function ($alumno) {
                return $alumno->apellidop . ' ' . $alumno->apellidom . ' ' . $alumno->nombre1;
            })
            ->values();
    }

    public function map($alumno): array
    {
        return [
            $alumno->nombre1,
            $alumno->nombre2,
            $alumno->apellidop,
            $alumno->apellidom,
            $alumno->email_institucional,
            $alumno->email_personal,
            $alumno->celular
        ];
    }

    public function startRow(): int
    {
        return 9; // Los datos comienzan en la fila 9
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A8:G8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'], // Azul marino
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Encabezado institucional
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ');
                $sheet->mergeCells('A4:G4');
                $sheet->setCellValue('A4', 'INSTITUTO DE POSGRADO');

                // Nombre de la maestría en varias líneas
                $fila = 5;
                $lineas = $this->separarTextoPorLineas($this->maestria_nombre, 40);
                foreach ($lineas as $linea) {
                    $sheet->mergeCells("A{$fila}:G{$fila}");
                    $sheet->setCellValue("A{$fila}", 'COORDINACIÓN DE LA ' . $linea);
                    $fila++;
                }

                // Título de la tabla
                $sheet->mergeCells("A7:G7");
                $sheet->setCellValue("A7", "LISTADO DE ESTUDIANTES");
                $sheet->getStyle("A7")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(20);

                // Encabezados de tabla manuales en la fila 8
                $encabezados = [
                    "Primer Nombre",
                    "Segundo Nombre",
                    "Apellido Paterno",
                    "Apellido Materno",
                    "Email Institucional",
                    "Email Personal",
                    "Celular"
                ];
                $col = 'A';
                foreach ($encabezados as $titulo) {
                    $sheet->setCellValue("{$col}8", $titulo);
                    $col++;
                }

                // Estilo institucional
                foreach (range(3, $fila - 1) as $row) {
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                // Autoajuste
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Bordes
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:G{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => 'center']
                ]);
            },
        ];
    }

    protected function separarTextoPorLineas($texto, $maxCaracteres)
    {
        $palabras = explode(' ', $texto);
        $lineas = [];
        $lineaActual = '';

        foreach ($palabras as $palabra) {
            if (strlen($lineaActual . ' ' . $palabra) <= $maxCaracteres) {
                $lineaActual .= ($lineaActual ? ' ' : '') . $palabra;
            } else {
                $lineas[] = $lineaActual;
                $lineaActual = $palabra;
            }
        }

        if ($lineaActual) {
            $lineas[] = $lineaActual;
        }

        return $lineas;
    }

    public function drawings()
    {
        $logo1 = new Drawing();
        $logo1->setName('Logo UNESUM');
        $logo1->setDescription('Logo UNESUM');
        $logo1->setPath(public_path('images/unesum.png'));
        $logo1->setHeight(90);
        $logo1->setCoordinates('A1');

        $logo2 = new Drawing();
        $logo2->setName('Logo Posgrado');
        $logo2->setDescription('Logo Posgrado');
        $logo2->setPath(public_path('images/posgrado-25.png'));
        $logo2->setHeight(120);
        $logo2->setCoordinates('G1');

        return [$logo1, $logo2];
    }
}
