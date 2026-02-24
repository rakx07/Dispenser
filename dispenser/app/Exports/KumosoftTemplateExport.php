<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KumosoftTemplateExport implements WithHeadings, FromArray, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'school_id',
            'lastname',
            'firstname',
            'middlename',
            'suffix',
            'email',
            'username',
            'password',
        ];
    }

    public function array(): array
    {
        return [];
    }
}