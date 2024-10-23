@extends('adminlte::page')

@section('title', 'Student Import')

@section('content_header')
<div class="container">
    <h2>Add New SATP User</h2>

    <!-- Display success message -->
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- Display validation and duplicate errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('satpaccount.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" required>
        </div>

        <div class="form-group">
            <label for="satp_password">Password</label>
            <input type="text" name="satp_password" class="form-control" value="{{ old('satp_password') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Add SATP Account</button>
    </form>
</div>
@endsection
