@extends('adminlte::page')

@section('content')
<div class="container">
    <h2 class="mb-4">Student Transactions</h2>

    <!-- Export Button -->
    <a href="{{ route('audit.transactions.export') }}" class="btn btn-success mb-3">
        Download Excel
    </a>

    <table class="table table-bordered">
        <thead>
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
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->student->school_id }}</td>
                    <td>{{ $transaction->student->firstname }}</td>
                    <td>{{ $transaction->student->middlename }}</td>
                    <td>{{ $transaction->student->lastname }}</td>
                    <td>{{ $transaction->student->course ? $transaction->student->course->name : 'N/A' }}</td>
                    <td>{{ $transaction->accessed_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
