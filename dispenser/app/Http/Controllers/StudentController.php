<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentUser;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // List students with pagination
    public function index()
    {
        $students = Student::paginate(20); // Adjust pagination as needed
        return view('students.index', compact('students'));
    }

    // Import students from an Excel file
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new StudentImport();
            Excel::import($import, $request->file('import_file'));

            $skippedCount = $import->skipped;
            $message = 'File imported successfully!';

            if ($skippedCount > 0) {
                $message .= " $skippedCount duplicate student/s were skipped.";
            }

            return redirect()->back()->with('status', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while importing the file: ' . $e->getMessage());
        }
    }

    // Edit a specific student by ID
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::where('status', '1')->get();
        return view('students.edit', compact('student', 'courses'));
    }

    // Update student information
    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'course_id' => 'required|exists:course,id',
            'birthday' => 'required|string',  // Ensure birthday is treated as a text field
            'status' => 'required|boolean',
        ]);

        $student = Student::findOrFail($id);
        $student->school_id = $request->school_id;
        $student->lastname = $request->lastname;
        $student->firstname = $request->firstname;
        $student->middlename = $request->middlename;
        $student->course_id = $request->course_id;
        $student->birthday = $request->birthday;
        $student->status = $request->status;
        $student->save();

        return redirect()->route('student.search')->with('status', 'Student updated successfully!');
    }

    // Delete a student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('status', 'Student deleted successfully!');
    }

    // Check if a student exists
    public function checkStudent(Request $request)
    {
        $request->validate([
            'courseSelect' => 'required',
            'idNumber' => 'required',
            'lastname' => 'required',
            'birthday' => 'required|date_format:Y-m-d',
        ]);

        $courseCode = $request->input('courseSelect');
        $idNumber = $request->input('idNumber');
        $lastname = $request->input('lastname');
        $birthday = $request->input('birthday');

        $course = Course::where('code', $courseCode)->first();

        if (!$course) {
            return back()->withErrors(['courseSelect' => 'Invalid course selected.']);
        }

        $studentExists = Student::where('school_id', $idNumber)
            ->where('lastname', $lastname)
            ->where('birthday', $birthday)
            ->where('course_id', $course->id)
            ->exists();

        if ($studentExists) {
            return back()->with(['showModal' => true, 'school_id' => $idNumber]);
        } else {
            return back()->with('message', 'Student does not exist.');
        }
    }

    // Create a new student account
    public function createStudentAccount(Request $request)
    {
        $request->validate([
            'schoolId' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        if (StudentUser::where('school_id', $request->input('schoolId'))->exists()) {
            return redirect()->route('welcome')->with('error', 'Student Account already exists.');
        }

        $studentUser = new StudentUser();
        $studentUser->school_id = $request->input('schoolId');
        $studentUser->password = Hash::make($request->input('password'));
        $studentUser->status = 1; // Active status
        $studentUser->save();

        return redirect()->route('signin')->with('message', 'Account created successfully.');
    }

    // Show the welcome view
    public function welcomeview()
    {
        $courses = Course::all(); // Fetch all courses from the database
        return view('welcome', compact('courses')); // Pass $courses to the view
    }

    // Add a new student (create view)
    public function create()
    {
        $courses = Course::where('status', '1')->get(); // Fetch active courses
        return view('students.create', compact('courses'));
    }

    // Store a new student
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|unique:students,school_id',
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'birthday' => 'required|string',  // Treat birthday as a text field
            'status' => 'required|boolean',
        ]);

        try {
            Student::create([
                'school_id' => $request->school_id,
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'course_id' => $request->course_id,
                'birthday' => $request->birthday,
                'status' => $request->status,
            ]);

            return redirect()->route('students.index')->with('success', 'Student added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('students.create')->with('error', 'Failed to add student. Please try again.');
        }
    }

    // Search for students by first name, last name, or school ID
    public function search(Request $request)
    {
        $query = $request->input('query');
        $students = Student::where('firstname', 'LIKE', "%$query%")
                            ->orWhere('lastname', 'LIKE', "%$query%")
                            ->orWhere('school_id', 'LIKE', "%$query%")
                            ->paginate(20); // Adjust pagination as needed

        return view('students.search', compact('students'));
    }
}
