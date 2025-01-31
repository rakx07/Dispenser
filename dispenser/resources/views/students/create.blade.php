@extends('adminlte::page')

@section('title', 'Add Student')

@section('content_header')
@endsection

@section('content')
<!-- Local Bootstrap CSS -->
<link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<!-- Local Bootstrap Select CSS -->
<link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
<!-- Local SweetAlert2 CSS -->
<link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 mt-5">
            <div class="card">
                <div class="card-header">
                    <h3>Add Student</h3>
                </div>
                <div class="card-body">
                    <form id="addStudentForm">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                            <select id="course_id" name="course_id" class="form-control selectpicker" data-live-search="true" data-size="8" required>
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
<link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"> <!-- Local SweetAlert2 CSS -->

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
<!-- Load jQuery Before Other Scripts -->
<script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script> <!-- Local SweetAlert2 -->

<script>
$(document).ready(function () {
    $('.selectpicker').selectpicker(); // Initialize Bootstrap Selectpicker

    // AJAX Form Submission
    $('#addStudentForm').submit(function (e) {
        e.preventDefault(); // Prevent page refresh

        let formData = $(this).serialize(); // Get form data

        $.ajax({
            url: "{{ route('student.store') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token
            },
            dataType: "json",
            success: function (response) {
                console.log("AJAX Success Response:", response); // Debugging
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.success,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload page after clicking OK
                    });

                    $('#addStudentForm')[0].reset(); // Clear form fields
                    $('.selectpicker').selectpicker('refresh'); // Refresh select dropdown
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (xhr) {
                console.error("AJAX Error Response:", xhr); // Debugging

                let errorMessage = "Failed to add student. Please try again.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Clear button functionality
    $('#clearButton').click(function() {
        $('#addStudentForm')[0].reset();
        $('.selectpicker').selectpicker('refresh');
    });
});
</script>
@endpush
