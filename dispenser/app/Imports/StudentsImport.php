<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);

        $course = Course::where('code', $row['course_code'] ?? '')->first();

        $schoolId = $row['school_id'] ?? null;
        $lastname = $row['lastname'] ?? '';
        $firstname = $row['firstname'] ?? '';
        $middlename = $row['middlename'] ?? '';
        $courseId = $course ? $course->id : null;
        $birthday = isset($row['birthday']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $row['birthday']) : null;
        $status = $row['status'] ?? '';

        // Log each individual value
        Log::info('School ID: ' . $schoolId);
        Log::info('Lastname: ' . $lastname);
        Log::info('Firstname: ' . $firstname);
        Log::info('Middlename: ' . $middlename);
        Log::info('Course ID: ' . $courseId);
        Log::info('Birthday: ' . ($birthday ? $birthday->format('Y-m-d') : ''));
        Log::info('Status: ' . $status);

        // Find or create the student record
        $student = Student::updateOrCreate(
            ['school_id' => $schoolId],
            [
                'lastname'   => $lastname,
                'firstname'  => $firstname,
                'middlename' => $middlename,
                'course_id'  => $courseId,
                'birthday'   => $birthday,
                'status'     => $status,
            ]
        );

        return $student;
    }
}