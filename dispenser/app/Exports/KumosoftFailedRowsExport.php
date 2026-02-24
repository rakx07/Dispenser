<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KumosoftFailedRowsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    private array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'school_id','lastname','firstname','middlename','suffix','email','username','password',
            'error_code','error_msg'
        ];
    }
}