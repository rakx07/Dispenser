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
    public function index()
    {
        // $students = Student::all();
        // return view('students.index', compact('students'));
        $students = Student::paginate(20); // Adjust the number per page as needed
        return view('students.index', compact('students'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new StudentImport, $request->file('import_file'));

        return redirect()->back()->with('status', 'File imported successfully!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::where('status', '1')->get();
        return view('student.edit', compact('student', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'course_id' => 'required|exists:courses,id',
            'birthday' => 'required',
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
        $student->voucher = $request->voucher_id;
        // $student->email = $request->email_id;
        $student->save();

        return redirect()->route('students.index')->with('status', 'Student updated successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('status', 'Student deleted successfully!');
    }

    //added check
    public function checkStudent(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'courseSelect' => 'required',
            'idNumber' => 'required',
            'lastname' => 'required',
            'birthday' => 'required|date_format:Y-m-d',
        ]);

        // Retrieve form data
        $courseCode = $request->input('courseSelect');
        $idNumber = $request->input('idNumber');
        $lastname = $request->input('lastname');
        $birthday = $request->input('birthday');

        // Fetch the course ID from the course code
        $course = Course::where('code', $courseCode)->first();

        if (!$course) {
            return back()->withErrors(['courseSelect' => 'Invalid course selected.']);
        }

        // Check if the student exists
        $studentExists = Student::where('school_id', $idNumber)
            ->where('lastname', $lastname)
            ->where('birthday', $birthday)
            ->where('course_id', $course->id)
            ->exists();

        // Return the appropriate response
        if ($studentExists) {
            // return back()->with('message', 'Student exists.');
            return back()->with(['showModal' => true, 'school_id' => $idNumber]);
        } else {
            return back()->with('message', 'Student does not exist.');
        }
        
    }
    public function createStudentAccount(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'schoolId' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);
    
        // Check if the student account already exists
        if (StudentUser::where('school_id', $request->input('schoolId'))->exists()) {
            return redirect()->route('welcome')->with('error', 'Student Account already exists.');
        }
    
        // Create the student account
        $studentUser = new StudentUser();
        $studentUser->school_id = $request->input('schoolId');
        $studentUser->password = Hash::make($request->input('password'));
        $studentUser->status = 1; // Active status
        $studentUser->save();
    
        return redirect()->route('signin')->with('message', 'Account created successfully.');
    }
    public function welcomeview()
    {
    $courses = Course::all(); // Fetch all courses from the database
    return view('welcome', compact('courses')); // Pass $courses to the view
}
}
