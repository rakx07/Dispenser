<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Student::with(['course', 'kumosoft'])->get()->map(function ($student) {
            return [
                'school_id' => $student->school_id,
                'firstname' => $student->firstname,
                'middlename' => $student->middlename,
                'lastname' => $student->lastname,
                'course' => $student->course->code ?? 'N/A',
                'birthday' => $student->birthday,
                'kumosoft_credentials' => $student->kumosoft->kumosoft_credentials ?? 'Not Available',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'School ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Course',
            'Birthday',
            'Kumosoft Credentials',
        ];
    }
}
