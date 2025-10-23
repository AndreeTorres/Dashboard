<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatisticsExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Remover la primera fila que son los encabezados
        return array_slice($this->data, 1);
    }

    public function headings(): array
    {
        // Usar la primera fila como encabezados
        return $this->data[0] ?? [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => ['font' => ['bold' => true]],
        ];
    }
}
