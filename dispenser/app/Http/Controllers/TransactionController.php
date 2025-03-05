<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Handle recording the action of showing the password.
     */
    public function recordShowPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $student = Student::findOrFail($request->student_id);

        // Check if a transaction already exists for this student
        $existingTransaction = Transaction::where('student_id', $student->id)->first();

        if (!$existingTransaction) {
            // Record the first time only, never update again
            Transaction::create([
                'student_id'  => $student->id,
                'accessed_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Transaction recorded successfully']);
        }

        // If a transaction already exists, do nothing (return success but no new record)
        return response()->json(['success' => false, 'message' => 'Transaction already exists']);
    }
    public function index()
    {
        $transactions = Transaction::with(['student.course'])->get();
    
        return view('audit.transaction', compact('transactions'));
    }
    
}
