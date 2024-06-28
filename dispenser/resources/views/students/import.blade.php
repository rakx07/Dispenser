<!-- resources/views/student/import.blade.php -->

@extends('adminlte::page')

@section('title', 'Student Import')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <h3>Student Table</h3>
                <hr>
                <table id="student-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>School ID</th>
                            <th>Lastname</th>
                            <th>Firstname</th>
                            <th>Middlename</th>
                            <th>Course</th>
                            <th>Birthday</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>{{ $student->school_id }}</td>
                                <td>{{ $student->lastname }}</td>
                                <td>{{ $student->firstname }}</td>
                                <td>{{ $student->middlename }}</td>
                                <td>{{ $student->course->name }}</td>
                                <td>{{ $student->birthday }}</td>
                                <td>{{ $student->status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="{{ url('student/edit/' . $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ url('student/delete/' . $student->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Import to Database</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('students/import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="import_file" class="form-control" />
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#student-table').DataTable({
                paging: true, // Enable pagination
                lengthChange: false, // Disable length change
                searching: false, // Disable search feature
                ordering: true, // Enable ordering (sorting)
                info: true, // Enable info display
                autoWidth: false, // Disable auto width calculation
                responsive: true // Enable responsiveness
            });
        });

        function confirmDelete() {
            return confirm('Are you sure you want to delete this student? This action cannot be undone.');
        }
    </script>
@endpush
