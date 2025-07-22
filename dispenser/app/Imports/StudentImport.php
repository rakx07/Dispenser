<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    public $skipped = 0;
    public $skippedRows = []; // ⬅️ For downloadable skipped list

    public function model(array $row)
    {
        try {
    $course = Course::where('code', $row['course_code'])->first();

    if (!$course) {
        throw new \Exception("Course with code '{$row['course_code']}' not found.");
    }

    $exists = Student::where('school_id', $row['school_id'])
        ->where('lastname', $row['lastname'])
        ->where('birthday', $row['birthday'])
        ->where('course_id', $course->id)
        ->exists();

    if ($exists) {
        throw new \Exception("Duplicate student: {$row['school_id']} - {$row['lastname']}, {$row['firstname']}");
    }

    return new Student([
        'school_id' => $row['school_id'],
        'lastname' => $row['lastname'],
        'firstname' => $row['firstname'],
        'middlename' => $row['middlename'],
        'course_id' => $course->id,
        'birthday' => $row['birthday'],
        'status' => $row['status'] ?? 1,
        'voucher_id' => $row['voucher_id'] ?? null,
        'email_id' => $row['email_id'] ?? null,
    ]);
} catch (\Exception $e) {
    $this->skipped++;
    $this->skippedRows[] = [
        'school_id' => $row['school_id'] ?? 'N/A',
        'lastname' => $row['lastname'] ?? 'N/A',
        'firstname' => $row['firstname'] ?? 'N/A',
        'reason' => 'Import error: ' . $e->getMessage(),
    ];
    return null;
}

    }
}