<?php

namespace App\Exports;

use App\Models\Alumno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;

class AlumnosExport implements FromCollection, WithHeadings, WithEvents, WithDrawings, WithCustomStartCell, WithStartRow, WithMapping
{
    protected $alumnosMatriculados;
    protected $maestria;
    protected $cohorte;
    protected $asignatura;

    public function __construct($alumnosMatriculados, $maestria = null, $cohorte = null, $asignatura = null)
    {
        $this->alumnosMatriculados = $alumnosMatriculados;
        $this->maestria = $maestria;
        $this->cohorte = $cohorte;
        $this->asignatura = $asignatura;
    }

    public function startCell(): string
    {
        return 'A9'; // encabezado de la tabla
    }

    public function startRow(): int
    {
        return 9;
    }

    public function collection()
    {
        return $this->alumnosMatriculados;
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
            $alumno->celular,
        ];
    }

    public function headings(): array
    {
        return [
            'Primer Nombre',
            'Segundo Nombre',
            'Apellido Paterno',
            'Apellido Materno',
            'Correo Institucional',
            'Correo Personal',
            'Celular',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Logos institucionales
                $logo1 = new Drawing();
                $logo1->setName('Logo UNESUM');
                $logo1->setDescription('Logo UNESUM');
                $logo1->setPath(public_path('images/unesum.png'));
                $logo1->setHeight(90);
                $logo1->setCoordinates('A1');
                $logo1->setWorksheet($sheet);

                $logo2 = new Drawing();
                $logo2->setName('Logo Posgrado');
                $logo2->setDescription('Logo Posgrado');
                $logo2->setPath(public_path('images/posgrado-25.png'));
                $logo2->setHeight(120);
                $logo2->setCoordinates('G1');
                $logo2->setWorksheet($sheet);

                // Encabezado institucional
                $sheet->mergeCells('A3:G3')->setCellValue('A3', 'UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ');
                $sheet->mergeCells('A4:G4')->setCellValue('A4', 'INSTITUTO DE POSGRADO');
                $sheet->getStyle('A3:G4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Información de maestría, cohorte y asignatura
                $sheet->mergeCells('A5:G5')->setCellValue('A5', 'Maestría: ' . ($this->maestria ?? 'N/A'));
                $sheet->mergeCells('A6:G6')->setCellValue('A6', 'Cohorte: ' . ($this->cohorte ?? 'N/A'));
                $sheet->mergeCells('A7:G7')->setCellValue('A7', 'Asignatura: ' . ($this->asignatura ?? 'N/A'));
                foreach (['A5','A6','A7'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                // Título del listado
                $sheet->mergeCells('A8:G8')->setCellValue('A8', 'LISTADO DE ESTUDIANTES');
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(20);

                // Estilo del encabezado de tabla (fila 9)
                $sheet->getStyle('A9:G9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Bordes de encabezado + datos
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A9:G{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);

                // Autoajuste columnas
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }

    public function drawings()
    {
        return [];
    }
}
