<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class StudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $course = Course::where('code', $row['course_code'])->first();

        return new Student([
            'school_id'  => $row['school_id'],
            'lastname'   => $row['lastname'],
            'firstname'  => $row['firstname'],
            'middlename' => $row['middlename'],
            'course_id'  => $course->id,
            // 'birthday'   => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birthday']),
            // 'birthday' => Carbon::createFromFormat('Y-d-m', $row['birthday'])->format('Y-d-m'),
            'birthday' => $row['birthday'],
            'status'     => $row['status'],
        ]);
    }
}