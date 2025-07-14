@extends('adminlte::page')

@section('title', 'Edit Student')

@section('content_header')
@endsection

@section('content')

<!-- Local Bootstrap CSS -->
<link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Local Bootstrap Select CSS -->
<link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 mt-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3>Edit Student</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="school_id">School ID:</label>
                            <input type="text" id="school_id" name="school_id" class="form-control" value="{{ $student->school_id }}" required>
                        </div>

                        <div class="form-group">
                            <label for="lastname">Last Name:</label>
                            <input type="text" id="lastname" name="lastname" class="form-control" value="{{ $student->lastname }}" required>
                        </div>

                        <div class="form-group">
                            <label for="firstname">First Name:</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" value="{{ $student->firstname }}" required>
                        </div>

                        <div class="form-group">
                            <label for="middlename">Middle Name:</label>
                            <input type="text" id="middlename" name="middlename" class="form-control" value="{{ $student->middlename }}">
                        </div>

                        <div class="form-group">
                            <label for="course_id">Course:</label>
                            <select id="course_id" name="course_id" class="form-control selectpicker" data-live-search="true" data-size="8" required>
                                <option value="" disabled>Select a course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-subtext="{{ $course->code }}"
                                        {{ $student->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Birthday (YYYY-MM-DD):</label>
                            <input type="text" id="birthday" name="birthday" class="form-control" value="{{ $student->birthday }}" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="1" {{ $student->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $student->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="voucher">Voucher ID:</label>
                            <div class="input-group">
                                <input type="text" id="voucher" name="voucher" class="form-control"
                                    value="{{ $student->voucher ? $student->voucher->voucher_code : '' }}" readonly>
                                <button type="button" class="btn btn-danger" onclick="generateVoucherCode()">Remove</button>
                            </div>

                            <div id="voucherDisplay" class="mt-1">
                                @if($student->voucher)
                                    <small id="currentVoucherDisplay" class="text-success">
                                        Current Voucher: <strong>{{ $student->voucher->voucher_code }}</strong>
                                    </small>
                                @else
                                    <small id="currentVoucherDisplay" class="text-muted">
                                        No voucher assigned to this student.
                                    </small>
                                @endif
                            </div>

                            <div id="voucher-alert" class="mt-2"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function generateVoucherCode() {
        if (!confirm("Are you sure you want to replace the voucher for this student?")) {
            return;
        }

        fetch('{{ route("voucher.remove", $student->id) }}')
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('voucher-alert');

                if (data.success) {
                    document.getElementById('voucher').value = data.voucher_code ?? '';

                    const display = document.getElementById('currentVoucherDisplay');
                    if (data.voucher_code) {
                        display.classList.remove('text-muted');
                        display.classList.add('text-success');
                        display.innerHTML = `Current Voucher: <strong>${data.voucher_code}</strong>`;
                    } else {
                        display.classList.remove('text-success');
                        display.classList.add('text-muted');
                        display.innerHTML = `No voucher assigned to this student.`;
                    }

                    alertContainer.innerHTML = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertContainer = document.getElementById('voucher-alert');
                alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    An unexpected error occurred.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            });
    }
</script>

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
<script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
@endpush
