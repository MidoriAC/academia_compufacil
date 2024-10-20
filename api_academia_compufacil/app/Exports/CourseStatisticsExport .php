<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CourseStatisticsExport implements FromCollection, WithHeadings
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
            'Título del Curso',
            'Total de Estudiantes',
            'Calificación Promedio'
        ];
    }
}