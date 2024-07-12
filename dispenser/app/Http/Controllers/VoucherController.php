<?php

namespace App\Http\Controllers;

use App\Imports\VoucherImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Voucher;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Str; // Import Str class

class VoucherController extends Controller
{
    // Display a paginated list of vouchers
    public function index()
    {
        $vouchers = Voucher::paginate(20);
        return view('voucher.index', compact('vouchers'));
    }

    // Import voucher data from an Excel file
    public function importExcelData(Request $request)
    {
        $request->validate([
            'import_file'=> [
                'required',
                'file',
            ]
        ]);

        Excel::import(new VoucherImport, $request->file('import_file'));
        return redirect()->back()->with('status', 'Excel import successful!');
    }

    // Show student and voucher information based on form submission
    public function show(Request $request)
    {
        $idNumber = $request->input('idNumber');
        $courseCode = $request->input('courseSelect');

        // Fetch the student data based on ID number and course code
        $student = Student::where('school_id', $idNumber)
                          ->whereHas('course', function($query) use ($courseCode) {
                              $query->where('code', $courseCode);
                          })
                          ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Check if the student already has a voucher assigned
        if ($student->voucher_id) {
            $voucher = Voucher::find($student->voucher_id);
        } else {
            // Fetch a voucher code that has not been given
            $voucher = Voucher::where('is_given', 0)->first();

            if (!$voucher) {
                return redirect()->back()->with('error', 'No available vouchers');
            }

            // Assign the voucher to the student
            $student->voucher_id = $voucher->id;
            $student->save();

            // Mark the voucher as given
            $voucher->is_given = 1;
            $voucher->save();
        }

        return view('voucher', compact('student', 'voucher'));
    }
}
