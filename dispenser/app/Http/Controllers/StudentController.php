<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentUser;
use App\Models\Course;
use App\Models\Voucher;
use App\Models\Email;
use App\Models\Satpaccount;
use App\Models\SchoologyCredential;
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
        //   dd($request->file('import_file')); // ← Check if file is received
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
            'status' => 1,
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
        'birthday' => 'required|string', // Treated as plain text
        'status' => 'required|boolean',
    ]);

    try {
        DB::beginTransaction(); // Start database transaction

        $student = Student::create([
            'school_id' => $request->school_id,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'course_id' => $request->course_id,
            'birthday' => $request->birthday,
            'status' => $request->status,
        ]);

        if (!$student) {
            throw new \Exception("Student record was not created.");
        }

        DB::commit(); // Commit transaction if successful

        return response()->json(['success' => 'Student added successfully!'], 200);
    
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback transaction in case of error

        \Log::error('Failed to add student: ' . $e->getMessage()); // Log the actual error

        return response()->json(['error' => 'Failed to add student. Please try again.'], 500);
    }
}



    // Show voucher details
   public function handleVoucherAndSatp(Request $request)
{
    $request->validate([
        'idNumber' => 'required|string',
        'lastname' => 'required|string',
        'birthday' => 'required|date_format:Y-m-d',
        'courseSelect' => 'required|string',
    ]);

    $school_id = $request->input('idNumber');

    $student = Student::where('school_id', $school_id)->first();
    $satpAccount = Satpaccount::where('school_id', $school_id)->first();
    $emailRecord = Email::where('sch_id_number', $school_id)->first();
    $schoology = SchoologyCredential::where('school_id', $school_id)->first();

    if ($student) {
        $satp_password = $satpAccount ? $satpAccount->satp_password : null;
        $voucher = $student->voucher_id
            ? Voucher::find($student->voucher_id)
            : $this->generateNewVoucherForStudent($student);

        return view('voucher', [
            'student' => $student,
            'satp_password' => $satp_password,
            'voucher' => $voucher,
            'email' => $emailRecord->email_address ?? null,
            'password' => $emailRecord->password ?? null,
            'schoology_credentials' => $schoology->schoology_credentials ?? null,
        ]);
    } else {
        return redirect()->back()->with('error', 'Student record not found.');
    }
}




    public function showVoucher()
    {
        return view('voucher', [
            'student' => session('student'),
            'email' => session('email'),
            'password' => session('password'),
            'voucher' => session('voucher'),
        ]);
    }

    private function generateNewVoucherForStudent($student)
    {
        DB::beginTransaction();

        try {
            $voucher = Voucher::where('is_given', 0)->first();

            if (!$voucher) {
                return redirect()->back()->with('error', 'No available vouchers');
            }

            $student->voucher_id = $voucher->id;
            $student->save();

            $voucher->is_given = 1;
            $voucher->save();

            DB::commit();

            return $voucher;
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
public function search(Request $request)
{
    $query = $request->input('query');

    $students = Student::with(['course', 'satp', 'schoology', 'email']) // ✅ Add 'email'
        ->when($query, function ($q) use ($query) {
            $q->where('firstname', 'LIKE', "%{$query}%")
              ->orWhere('lastname', 'LIKE', "%{$query}%")
              ->orWhere('school_id', 'LIKE', "%{$query}%");
        })->paginate(10);

    return view('students.search', compact('students'));
}


}
