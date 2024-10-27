<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentUser;
use App\Models\Course;
use App\Models\Voucher;
use App\Models\Satpaccount;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // List students with pagination
    public function index()
    {
        $students = Student::paginate(20);
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
            'birthday' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $student = Student::findOrFail($id);
        $student->update($request->only([
            'school_id', 'lastname', 'firstname', 'middlename', 'course_id', 'birthday', 'status'
        ]));

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

        $course = Course::where('code', $request->input('courseSelect'))->first();

        if (!$course) {
            return back()->withErrors(['courseSelect' => 'Invalid course selected.']);
        }

        $studentExists = Student::where([
            ['school_id', $request->input('idNumber')],
            ['lastname', $request->input('lastname')],
            ['birthday', $request->input('birthday')],
            ['course_id', $course->id]
        ])->exists();

        return $studentExists
            ? back()->with(['showModal' => true, 'school_id' => $request->input('idNumber')])
            : back()->with('message', 'Student does not exist.');
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

        StudentUser::create([
            'school_id' => $request->input('schoolId'),
            'password' => Hash::make($request->input('password')),
            'status' => 1, // Active status
        ]);

        return redirect()->route('signin')->with('message', 'Account created successfully.');
    }

    // Show the welcome view
    public function welcomeview()
    {
        $courses = Course::all();
        return view('welcome', compact('courses'));
    }

    // Add a new student (create view)
    public function create()
    {
        $courses = Course::where('status', '1')->get();
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
            'course_id' => 'required|exists:course,id',
            'birthday' => 'required|string',
            'status' => 'required|boolean',
        ]);

        try {
            Student::create($request->only([
                'school_id', 'lastname', 'firstname', 'middlename', 'course_id', 'birthday', 'status'
            ]));

            return redirect()->route('students.search')->with('success', 'Student added successfully!');
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
                            ->paginate(20);

        return view('students.search', compact('students'));
    }

    // Show a voucher for a student by ID
    public function handleVoucherAndSatp(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'idNumber' => 'required|string',
            'lastname' => 'required|string',
            'birthday' => 'required|date_format:Y-m-d',
            'courseSelect' => 'required|string',
        ]);
    
        $school_id = $request->input('idNumber');
    
        // Find the student and SATP account data
        $student = Student::where('school_id', $school_id)->first();
        $satpAccount = Satpaccount::where('school_id', $school_id)->first();
    
        // Check if student and SATP account were found
        if ($student && $satpAccount) {
            $satp_password = $satpAccount->satp_password;
    
            // Check if the student has an assigned voucher
            if ($student->voucher_id) {
                // If a voucher exists, retrieve it
                $voucher = Voucher::find($student->voucher_id);
            } else {
                // If no voucher is assigned, generate a new one
                $voucher = $this->generateNewVoucherForStudent($student);
            }
    
            return view('voucher', [
                'student' => $student,
                'satp_password' => $satp_password,
                'voucher' => $voucher, // Pass the voucher to the view
            ]);
        } else {
            return redirect()->back()->with('error', 'Student or SATP account not found.');
        }
    }
    
    private function generateNewVoucherForStudent($student)
    {
        // Start a transaction
        DB::beginTransaction();
    
        try {
            // Fetch a new voucher code that has not been given
            $voucher = Voucher::where('is_given', 0)->first();
    
            if (!$voucher) {
                return redirect()->back()->with('error', 'No available vouchers');
            }
    
            // Assign the new voucher to the student
            $student->voucher_id = $voucher->id;
            $student->save();
    
            // Mark the new voucher as given
            $voucher->is_given = 1;
            $voucher->save();
    
            // Commit the transaction
            DB::commit();
    
            return $voucher; // Return the newly generated voucher
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    
}
