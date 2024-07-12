<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Voucher;
use App\Models\Course;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VoucherController extends Controller
{
    // ...

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

        // Check if the student already has a voucher assigned
        if ($student->voucher_id !== null) {
            $voucher = Voucher::find($student->voucher_id);
        } else {
            // Fetch a voucher code that has not been given
            $voucher = Voucher::where('is_given', 0)->first();
        }

        if ($student && $voucher) {
            // If the student does not have a voucher assigned, assign it
            if ($student->voucher_id === null) {
                // Assign the voucher_id to the student
                $student->voucher_id = $voucher->id;
                $student->save();

                // Mark the voucher as given
                $voucher->is_given = 1;
                $voucher->save();
            }

            return view('voucher', compact('student', 'voucher'));
        } else {
            return redirect()->back()->with('error', 'Student or voucher not found');
        }
    }

    // ...
}
