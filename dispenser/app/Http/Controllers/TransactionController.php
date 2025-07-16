<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Transaction;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function recordShowPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $student = Student::findOrFail($request->student_id);

        $existingTransaction = Transaction::where('student_id', $student->id)->first();

        if (!$existingTransaction) {
            Transaction::create([
                'student_id'  => $student->id,
                'accessed_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Transaction recorded successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Transaction already exists']);
    }

    public function index()
    {
        $transactions = Transaction::with(['student.course'])
                            ->latest()
                            ->paginate(10);

        $totalTransactions = Transaction::count();

        return view('audit.transaction', compact('transactions', 'totalTransactions'));
    }

    public function export()
    {
        return Excel::download(new TransactionsExport, 'student_transactions.xlsx');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $transactions = Transaction::with(['student.course'])
            ->whereHas('student', function ($q) use ($query) {
                $q->where('school_id', 'like', "%$query%")
                  ->orWhere('firstname', 'like', "%$query%")
                  ->orWhere('lastname', 'like', "%$query%")
                  ->orWhere('middlename', 'like', "%$query%");
            })
            ->orWhereHas('student.course', function ($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->latest()
            ->paginate(10);

        return view('audit.partials.transaction_table', compact('transactions'))->render();
    }
}
