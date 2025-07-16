<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Transaction;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

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
    // Fetch transactions with their associated student and course
    $transactions = Transaction::with(['student.course'])
                        ->latest()
                        ->paginate(10); // Change this as needed for pagination

    // Get the total number of transactions
    $totalTransactions = Transaction::count(); // Count all transactions

    // Pass both transactions and totalTransactions to the view
    return view('audit.transaction', compact('transactions', 'totalTransactions'));
}
    public function export()
    {
        return Excel::download(new TransactionsExport, 'student_transactions.xlsx');
    }
    
    
}
