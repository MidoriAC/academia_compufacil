<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentStatisticsExport implements FromCollection, WithHeadings
{
    protected $statistics;

    public function __construct($statistics)
    {
        $this->statistics = $statistics;
    }

    public function collection()
    {
        return collect($this->statistics);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Email',
            'Total de Cursos',
            'Progreso Promedio'
        ];
    }
}