@extends('adminlte::page')

@section('title', 'Add Student')

@section('content_header')
    {{-- <h1>Add Student</h1> --}}
@endsection

@section('content')
 <!-- Local Bootstrap CSS -->
 <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
 <!-- Local Bootstrap Select CSS -->
 <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
 
 <!-- Local SweetAlert2 JS -->
 <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
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

                            <!-- School ID Input -->
                            <div class="form-group">
                                <label for="school_id">School ID:</label>
                                <input type="text" id="school_id" name="school_id" class="form-control" required>
                            </div>

                            <!-- Last Name Input -->
                            <div class="form-group">
                                <label for="lastname">Last Name:</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" required>
                            </div>

                            <!-- First Name Input -->
                            <div class="form-group">
                                <label for="firstname">First Name:</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" required>
                            </div>

                            <!-- Middle Name Input -->
                            <div class="form-group">
                                <label for="middlename">Middle Name:</label>
                                <input type="text" id="middlename" name="middlename" class="form-control">
                            </div>

                            <!-- Course Selection with Bootstrap Select -->
                            <div class="form-group">
                                <label for="course_id">Course:</label>
                                <select id="course_id" name="course_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="" disabled selected>Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" data-subtext="{{ $course->code }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Birthday Input -->
                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="text" id="birthday" name="birthday" class="form-control" placeholder="YYYY-MM-DD" required>
                            </div>

                            <!-- Status Selection -->
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Form Buttons -->
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
    <!-- Include Bootstrap and Bootstrap Select CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

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
        .large-selectpicker {
            width: 100% !important;
        }
        .bootstrap-select .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
            white-space: normal;
        }
    </style>
@endpush

@push('js')
    <!-- Include Bootstrap, jQuery, and Bootstrap Select JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap Selectpicker
            $('.selectpicker').selectpicker();

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
