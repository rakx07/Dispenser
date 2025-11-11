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
     * Create a transaction EVERY time credentials are accessed.
     * Expects: POST student_id
     * Returns: JSON
     */
    public function recordShowPassword(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        Transaction::create([
            'student_id'  => $validated['student_id'],
            'accessed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaction recorded.',
        ]);
    }

    /**
     * Paginated list page
     */
    public function index()
    {
        $transactions = Transaction::with(['student.course'])
            ->latest()
            ->paginate(10);

        $totalTransactions = Transaction::count();

        return view('audit.transaction', compact('transactions', 'totalTransactions'));
    }

    /**
     * Excel export (requires maatwebsite/excel)
     */
    public function export()
    {
        return Excel::download(new TransactionsExport, 'student_transactions.xlsx');
    }

    /**
     * AJAX search (kept tightly grouped to avoid loose ORs)
     */
    public function search(Request $request)
    {
        $query = (string) $request->get('query', '');

        $transactions = Transaction::with(['student.course'])
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->whereHas('student', function ($sq) use ($query) {
                        $sq->where('school_id', 'like', "%{$query}%")
                           ->orWhere('firstname', 'like', "%{$query}%")
                           ->orWhere('lastname', 'like', "%{$query}%")
                           ->orWhere('middlename', 'like', "%{$query}%");
                    })
                    ->orWhereHas('student.course', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    });
                });
            })
            ->latest()
            ->paginate(10);

        return view('audit.partials.transaction_table', compact('transactions'))->render();
    }
}
