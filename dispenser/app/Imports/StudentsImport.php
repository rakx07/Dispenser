<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $course = Course::where('code', $row['course_code'])->first();

        return new Student([
            'school_id'   => $row['school_id'],
            'lastname'    => $row['lastname'],
            'firstname'   => $row['firstname'],
            'middlename'  => $row['middlename'],
            'course_id'   => $course ? $course->id : null,
            'birthday'    => \Carbon\Carbon::createFromFormat('Y-m-d', $row['birthday']), // Ensure date format is correct
            'status'      => $row['status'],
        ]);
    }
}