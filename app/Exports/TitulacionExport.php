<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class TitulacionExport implements FromCollection, WithMapping, WithStyles, WithEvents, WithDrawings, WithStartRow, WithCustomStartCell
{
    protected $alumnos, $maestria, $cohorte;

    public function __construct($alumnos, $maestria, $cohorte)
    {
        $this->alumnos  = $alumnos;
        $this->maestria = $maestria;
        $this->cohorte  = $cohorte;
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
        $tesis = $alumno->tesis->first(); // suponiendo que solo consideras la primera tesis
        $titulacion = $tesis?->titulaciones->sortBy('fecha_graduacion')->first();

        return [
            $this->maestria->codigo,
            preg_match('/[a-zA-Z]/', $alumno->dni) ? 'PASAPORTE' : 'CÉDULA',
            $alumno->dni,
            $alumno->sexo === 'M' ? 'Masculino' : ($alumno->sexo === 'F' ? 'Femenino' : 'No especificado'),
            $alumno->email_institucional,
            $this->cohorte->fecha_inicio,
            'JIPIJAPA',
            $titulacion->fecha_graduacion ?? 'Sin titulación',
            $tesis->tipo ?? 'Sin tesis',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A8:I8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
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
                $logo2->setCoordinates('I1');
                $logo2->setWorksheet($sheet);

                // Encabezado institucional
                $sheet->mergeCells('A3:I3')->setCellValue('A3', 'UNIVERSIDAD ESTATAL DEL SUR DE MANABÍ');
                $sheet->mergeCells('A4:I4')->setCellValue('A4', 'INSTITUTO DE POSGRADO');
                $sheet->getStyle('A3:I4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Información de maestría y cohorte
                $sheet->mergeCells('A5:I5')->setCellValue('A5', '' . strtoupper($this->maestria->nombre));
                $sheet->mergeCells('A6:I6')->setCellValue('A6', '' . strtoupper($this->cohorte->nombre));
                foreach (['A5','A6'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                // Título del listado
                $sheet->mergeCells('A7:I7')->setCellValue('A7', 'LISTADO DE TITULACIÓN');
                $sheet->getStyle('A7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(20);

                // Encabezado de tabla
                $encabezados = [
                    "CODIGO_CARRERA",
                    "TIPO_IDENTIFICACION",
                    "IDENTIFICACION",
                    "SEXO",
                    "EMAIL_INSTITUCIONAL",
                    "FECHA_INICIO_PRIMER_NIVEL",
                    "CIUDAD_CARRERA",
                    "FECHA_GRADUACION",
                    "TIPO_TRABAJO_GRADUACION"
                ];
                $col = 'A';
                foreach ($encabezados as $titulo) {
                    $sheet->setCellValue("{$col}8", $titulo);
                    $col++;
                }

                // Estilo encabezado de tabla
                $sheet->getStyle('A8:I8')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Bordes y autoajuste
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:I{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
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
        $logo2->setCoordinates('I1');

        return [$logo1, $logo2];
    }
}
