@extends('adminlte::page')

@section('title', 'Edit Student')

@section('content_header')
    <h1>Edit Student</h1>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 mt-5">
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
                                <select id="course_id" name="course_id" class="form-control" required>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ $student->course_id == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" class="form-control" value="{{ $student->birthday }}" required>
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
                                    <input type="text" id="voucher" name="voucher" class="form-control" value="{{ $student->voucher->voucher_code ?? '' }}" readonly>
                                    <button type="button" class="btn btn-secondary" onclick="generateVoucherCode()">Generate</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Student</button>
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

@push('scripts')
    <script>
        function generateVoucherCode() {
            fetch('{{ route("voucher.generate", $student->id) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('voucher').value = data.voucher_code;
                    } else {
                        alert('No available vouchers.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching voucher:', error);
                });
        }
    </script>
@endpush
