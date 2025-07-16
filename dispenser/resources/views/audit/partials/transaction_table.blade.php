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

    <div class="d-flex justify-content-center">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>
