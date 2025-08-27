<?php

namespace App\Exports;

use App\Models\Alumno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class EstudiantesExport implements FromCollection, WithMapping, WithEvents, WithDrawings, WithCustomStartCell, WithStartRow
{
    protected $alumnos, $maestria, $cohorte, $asignatura;

    public function __construct($alumnos, $maestria = null, $cohorte = null, $asignatura = null)
    {
        $this->alumnos = $alumnos;
        $this->maestria = $maestria;
        $this->cohorte = $cohorte;
        $this->asignatura = $asignatura;
    }

    public function startCell(): string
    {
        return 'A9';
    }

    public function startRow(): int
    {
        return 9;
    }

    public function collection()
    {
        return $this->alumnos;
    }

    public function map($alumno): array
    {
        // Obtener nombre del descuento asociado a la maestría
        $descuento = $alumno->descuentos
            ->firstWhere('pivot.maestria_id', $this->maestria->id);
        $politicaCuota = $descuento ? $descuento->nombre : 'Sin descuento';

        return [
            $alumno->nombre1,
            $alumno->nombre2,
            $alumno->apellidop,
            $alumno->apellidom,
            $alumno->email_institucional,
            $alumno->email_personal,
            $alumno->celular,
            $politicaCuota,
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
                $sheet->mergeCells('A3:H3')->setCellValue('A3', 'UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ');
                $sheet->mergeCells('A4:H4')->setCellValue('A4', 'INSTITUTO DE POSGRADO');
                $sheet->getStyle('A3:H4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Información de maestría, cohorte y asignatura
                $sheet->mergeCells('A5:H5')->setCellValue('A5', '' . ($this->maestria->nombre ?? 'N/A'));
                $sheet->mergeCells('A6:H6')->setCellValue('A6', '' . ($this->cohorte->nombre ?? 'N/A'));
                foreach (['A5','A6','A7'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                // Título del listado
                $sheet->mergeCells('A8:H8')->setCellValue('A8', 'LISTADO DE ESTUDIANTES');
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(20);

                // Estilo del encabezado de tabla (fila 9)
                $sheet->getStyle('A9:H9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Bordes de encabezado + datos
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A9:H{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);

                // Autoajuste columnas
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
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
            'Política de Cuota',
        ];
    }
}
