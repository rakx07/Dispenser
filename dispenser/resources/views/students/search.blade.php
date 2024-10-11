@extends('adminlte::page')

@section('title', 'Search Student')

@section('content_header')
    <h1>Search Student</h1>
@endsection

@section('content')
    <div class="container">
        <form action="{{ route('student.search') }}" method="GET">
            <div class="input-group mb-3">
                <input type="text" name="query" class="form-control" placeholder="Search by ID/Name.." aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </div>
        </form>

        @if(isset($students) && $students->count() > 0)
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>School ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Course</th>
                        <th>Birthday</th>
                        <th>Status</th>
                        <th>Actions</th> <!-- Add this for the Edit button -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->school_id }}</td>
                            <td>{{ $student->firstname }}</td>
                            <td>{{ $student->lastname }}</td>
                            <td>{{ $student->course->name }}</td>
                            <td>{{ $student->birthday }}</td>
                            <td>{{ $student->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <!-- Edit button linking to the edit route -->
                                <a href="{{ route('student.edit', $student->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $students->links() }} <!-- Pagination Links -->
        @else
            <p>No students found matching your search.</p>
        @endif
    </div>
@endsection
