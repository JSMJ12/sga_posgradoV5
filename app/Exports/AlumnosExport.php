<?php

namespace App\Exports;

use App\Models\Alumno;
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
use Maatwebsite\Excel\Concerns\WithHeadings;
class AlumnosExport implements FromCollection, WithHeadings, WithEvents, WithDrawings, WithCustomStartCell, WithStartRow, WithMapping
{
    public function startCell(): string
    {
        return 'A9';
    }
    public function startRow(): int
    {
        return 9; // Los datos comienzan en la fila 9
    }
    protected $alumnosMatriculados;

    public function __construct($alumnosMatriculados)
    {
        $this->alumnosMatriculados = $alumnosMatriculados;
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

                // Encabezado institucional
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ');
                $sheet->mergeCells('A4:G4');
                $sheet->setCellValue('A4', 'INSTITUTO DE POSGRADO');

                // Aplica negrita y centrado a los encabezados institucionales
                $sheet->getStyle('A3:G4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Título
                $sheet->mergeCells('A6:G6');
                $sheet->setCellValue('A6', 'LISTADO DE ESTUDIANTES');
                $sheet->getStyle('A6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(20);

                // Encabezados tabla en fila 8
                $sheet->fromArray($this->headings(), null, 'A8');
                $sheet->getStyle('A8:G8')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Autoajuste columnas
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Bordes de encabezado + datos
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:G{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);
            }
        ];
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
