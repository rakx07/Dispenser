@extends('adminlte::page')

@section('title', 'Student Transactions')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Student Transactions</h2>
        <a href="{{ route('audit.transactions.export') }}" class="btn btn-success">
            Download Excel
        </a>
    </div>

    <div class="mb-3">
        <p>Total Transactions: <strong>{{ $totalTransactions }}</strong></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="thead-dark">
                <tr>
                    <th>School ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Course</th>
                    <th>Accessed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->student->school_id }}</td>
                        <td>{{ $transaction->student->firstname }}</td>
                        <td>{{ $transaction->student->middlename }}</td>
                        <td>{{ $transaction->student->lastname }}</td>
                        <td>{{ $transaction->student->course->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->accessed_at)->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
