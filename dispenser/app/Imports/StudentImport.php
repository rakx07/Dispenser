<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    public $skipped = 0; // Track the number of skipped records

    public function model(array $row)
    {
        $course = Course::where('code', $row['course_code'])->first();

        $existingStudent = Student::where('school_id', $row['school_id'])
            ->where('lastname', $row['lastname'])
            ->where('birthday', $row['birthday'])
            ->where('course_id', $course->id)
            ->first();

        if ($existingStudent) {
            $this->skipped++; // Increment the skipped counter if the student exists
            return null;
        }

        return new Student([
            'school_id'  => $row['school_id'],
            'lastname'   => $row['lastname'],
            'firstname'  => $row['firstname'],
            'middlename' => $row['middlename'],
            'course_id'  => $course->id,
            'birthday'   => $row['birthday'],
            'status'     => $row['status'],
            'voucher_id' => $row['voucher_id'],
            'email_id'   => $row['email_id'],
        ]);
    }
}
