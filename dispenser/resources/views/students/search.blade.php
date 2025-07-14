@extends('adminlte::page')

@section('title', 'Search Student')

@section('content_header')
    <h1>Search Student</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('student.search') }}" method="GET">
            <div class="input-group mb-3">
                <input type="text" name="query" class="form-control" placeholder="Search by ID/Name.." aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </div>
        </form>

        @if(isset($students) && $students->count() > 0)
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
                        @foreach($students as $student)
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $students->links() }} <!-- Bootstrap pagination -->
            </div>
        @else
            <p>No students found matching your search.</p>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 0.75rem;
            height: 60px;
            white-space: nowrap;
        }

        /* Optional fixed column widths for consistency */
        .table th:nth-child(1), .table td:nth-child(1) { width: 110px; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 120px; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 120px; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 180px; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 110px; }
        .table th:nth-child(6), .table td:nth-child(6) { width: 90px; }
        .table th:nth-child(7), .table td:nth-child(7) { width: 150px; }
        .table th:nth-child(8), .table td:nth-child(8) { width: 200px; }
        .table th:nth-child(9), .table td:nth-child(9) { width: 200px; }
        .table th:nth-child(10), .table td:nth-child(10) { width: 150px; }
        .table th:nth-child(11), .table td:nth-child(11) { width: 90px; }
    </style>
@endpush
