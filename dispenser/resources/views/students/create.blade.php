@extends('adminlte::page')

@section('title', 'Add Student')

@section('content_header')
    <h1>Add Student</h1>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h3>Add Student</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('student.store') }}" method="POST" id="addStudentForm">
                            @csrf

                            <div class="form-group">
                                <label for="school_id">School ID:</label>
                                <input type="text" id="school_id" name="school_id" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="lastname">Last Name:</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="firstname">First Name:</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="middlename">Middle Name:</label>
                                <input type="text" id="middlename" name="middlename" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="course_id">Course:</label>
                                <select id="course_id" name="course_id" class="form-control selectpicker" required>
                                    <option value="" disabled selected>Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="text" id="birthday" name="birthday" class="form-control" placeholder="YYYY-MM-DD" required>
                            </div>

                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Student</button>
                            <button type="button" class="btn btn-secondary" id="clearButton">Clear</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-group label {
            font-weight: bold;
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Clear button functionality
            document.getElementById('clearButton').addEventListener('click', function() {
                document.getElementById('addStudentForm').reset();
                $('.selectpicker').selectpicker('refresh');  // Refresh the selectpicker dropdown
            });

            // SweetAlert for Success or Error messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@endpush
