<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentUser;
use App\Models\Course;
use App\Models\Voucher;
use App\Models\Email;
use App\Models\Satpaccount;
use App\Models\Kumosoft;
use App\Models\SchoologyCredential;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentsExport;
use App\Models\CredentialDisplaySetting;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::paginate(20);
        return view('students.index', compact('students'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new StudentImport();
            Excel::import($import, $request->file('import_file'));

            if (!empty($import->skippedRows)) {
                $filename = 'skipped_students_' . now()->format('Ymd_His') . '.csv';
                $csvPath = storage_path("app/public/{$filename}");

                $file = fopen($csvPath, 'w');
                fputcsv($file, ['school_id', 'lastname', 'firstname', 'reason']);
                foreach ($import->skippedRows as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);

                $downloadUrl = asset("storage/{$filename}");

                return redirect()->back()
                    ->with('status', "Import complete. {$import->skipped} student(s) skipped. ")
                    ->with('download_skipped', $downloadUrl);
            }

            return redirect()->back()->with('status', 'File imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::where('status', '1')->get();
        return view('students.edit', compact('student', 'courses'));
    }

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

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('status', 'Student deleted successfully!');
    }

    public function checkStudent(Request $request)
    {
        $request->validate([
            'courseSelect' => 'required',
            'idNumber' => 'required|string',
            'lastname' => 'required|string',
            'birthday' => 'required|date_format:Y-m-d',
        ]);

        $idNumber = trim($request->input('idNumber'));
        $lastname = strtolower(trim($request->input('lastname')));
        $birthdayInput = trim($request->input('birthday'));

        try {
            $birthdayFormatted = Carbon::parse($birthdayInput)->format('Y-m-d');
        } catch (\Exception $e) {
            return back()->withErrors(['birthday' => 'Invalid date format.']);
        }

        $course = Course::where('code', $request->input('courseSelect'))->first();
        if (!$course) {
            return back()->withErrors(['courseSelect' => 'Invalid course selected.']);
        }

        $student = Student::where('school_id', $idNumber)
            ->whereRaw('LOWER(lastname) = ?', [$lastname])
            ->where('birthday', $birthdayFormatted)
            ->where('course_id', $course->id)
            ->first();

        if ($student) {
            return back()->with([
                'showModal' => true,
                'school_id' => $idNumber,
            ]);
        }

        return back()->with('message', 'Student does not exist or details do not match.');
    }

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

    public function welcomeview()
    {
        $courses = Course::all();
        return view('welcome', compact('courses'));
    }

    public function create()
    {
        $courses = Course::where('status', '1')->get();
        return view('students.create', compact('courses'));
    }

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
            DB::beginTransaction();

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

            DB::commit();
            return response()->json(['success' => 'Student added successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to add student: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add student. Please try again.'], 500);
        }
    }

    public function handleVoucherAndSatp(Request $request)
{
    $request->validate([
        'idNumber' => 'required|string',
        'lastname' => 'required|string',
        'birthday' => 'required|date_format:Y-m-d',
        'courseSelect' => 'required|string',
    ]);

    $idNumber = trim($request->input('idNumber'));              // can be EIS ID or Kumosoft ID
    $lastname = strtolower(trim($request->input('lastname')));
    $birthday = Carbon::parse($request->input('birthday'))->format('Y-m-d');

    $course = Course::where('code', $request->input('courseSelect'))->first();
    if (!$course) {
        return redirect()->back()->with('error', 'Invalid course selected.');
    }

    /**
     * ✅ Accept BOTH:
     * - EIS ID  (students.school_id)
     * - Kumosoft ID (kumosofts.kumosoft_school_id) -> map to eis_school_id
     */
    $eisIdToUse = $idNumber;

    // If typed ID is not a student EIS ID, try mapping from Kumosoft ID -> EIS ID
    $existsInStudents = Student::where('school_id', $eisIdToUse)->exists();
    if (!$existsInStudents) {
        $kmap = Kumosoft::where('kumosoft_school_id', $idNumber)->first();
        if ($kmap && !empty($kmap->eis_school_id)) {
            $eisIdToUse = $kmap->eis_school_id;
        }
    }

    // Strict student check remains the same (lastname + birthday + course)
    $student = Student::where('school_id', $eisIdToUse)
        ->whereRaw('LOWER(lastname) = ?', [$lastname])
        ->where('birthday', $birthday)
        ->where('course_id', $course->id)
        ->first();

    if (!$student) {
        return redirect()->back()->with('error', 'Student record not found or information does not match.');
    }

    if ((int) $student->status !== 1) {
        return redirect()->back()->with('error', 'Your account is not active. Please proceed to MIS Office.');
    }

    // IMPORTANT: use EIS ID for fetching other credentials
    $satpAccount = Satpaccount::where('school_id', $eisIdToUse)->first();
    $emailRecord = Email::where('sch_id_number', $eisIdToUse)->first();
    $schoology   = SchoologyCredential::where('school_id', $eisIdToUse)->first();

    /**
     * ✅ Kumosoft record:
     * - primary: eis_school_id = EIS ID
     * - fallback: kumosoft_school_id = typed ID (in case kiosk typed kumosoft id)
     */
    $kumosoft = Kumosoft::where('eis_school_id', $eisIdToUse)
        ->orWhere('kumosoft_school_id', $idNumber)
        ->first();

    $satp_password = $satpAccount ? $satpAccount->satp_password : null;

    $voucher = $student->voucher_id
        ? Voucher::find($student->voucher_id)
        : $this->generateNewVoucherForStudent($student);

    // Display settings
    $displaySettings = CredentialDisplaySetting::all()->keyBy('section');

    return view('voucher', [
        'student' => $student,
        'satp_password' => $satp_password,
        'voucher' => $voucher,

        'email' => $emailRecord->email_address ?? null,
        'password' => $emailRecord->password ?? null,

        'schoology_credentials' => optional($schoology)->schoology_credentials,

        // Backward compatible (if older code still uses it somewhere)
        'kumosoft_credentials' => optional($kumosoft)->kumosoft_credentials,

        // ✅ New fields for kiosk display
        'kumosoft_eis_id'   => $student->school_id,
        'kumosoft_id'       => optional($kumosoft)->kumosoft_school_id,
        'kumosoft_email'    => optional($kumosoft)->email,
        'kumosoft_password' => optional($kumosoft)->password,

        'displaySettings' => $displaySettings,
    ]);
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

        $students = Student::with(['course', 'satp', 'schoology', 'email'])
            ->when($query, function ($q) use ($query) {
                $q->where('firstname', 'LIKE', "%{$query}%")
                    ->orWhere('lastname', 'LIKE', "%{$query}%")
                    ->orWhere('school_id', 'LIKE', "%{$query}%");
            })->paginate(10);

        return view('students.search', compact('students'));
    }

    public function searchAjax(Request $request)
    {
        $query = $request->input('query');

        $students = Student::with(['course', 'satp', 'schoology', 'email'])
            ->when($query, function ($q) use ($query) {
                $q->where('school_id', 'LIKE', "%{$query}%")
                    ->orWhere('firstname', 'LIKE', "%{$query}%")
                    ->orWhere('lastname', 'LIKE', "%{$query}%");
            })
            ->paginate(10);

        return view('students.partials.student_table', compact('students'))->render();
    }

    public function exportExcel()
    {
        return Excel::download(new StudentsExport, 'students_with_kumosoft.xlsx');
    }
}