<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-dark">
            <tr>
                <th>School ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Course</th>
                <th>Birthday</th>
                <th>Status</th>
                <th>SATP Password</th>
                <th>Schoology Credentials</th>
                <th>Email</th>
                <th>Email Password</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->school_id }}</td>
                    <td>{{ $student->firstname }}</td>
                    <td>{{ $student->lastname }}</td>
                    <td>{{ $student->course->name ?? '-' }}</td>
                    <td>{{ $student->birthday }}</td>
                    <td>
                        <span class="badge {{ $student->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $student->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $student->satp->satp_password ?? '-' }}</td>
                    <td>{{ $student->schoology->schoology_credentials ?? '-' }}</td>
                    <td>{{ $student->email->email_address ?? '-' }}</td>
                    <td>{{ $student->email->password ?? '-' }}</td>
                    <td>
                        <a href="{{ route('student.edit', $student->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No matching students found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $students->links('pagination::bootstrap-4') }}
    </div>
</div>
