@extends('adminlte::page')

@section('title', 'Student Import')

@section('content_header')
<div class="container mt-5" style="max-width: 50%;">

    <h2 class="text-center mb-4">Add New SATP User</h2>

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

    <form action="{{ route('satpaccount.store') }}" method="POST" class="p-4 border rounded shadow-sm">
        @csrf

        <div class="form-group mb-3">
            <label for="school_id" class="form-label">Student ID</label>
            <input type="text" name="school_id" class="form-control" value="{{ old('school_id') }}" required>
        </div>

        <div class="form-group mb-4">
            <label for="satp_password" class="form-label">Password</label>
            <input type="text" name="satp_password" class="form-control" value="{{ old('satp_password') }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add SATP Account</button>
    </form>
</div>
@endsection