@extends('adminlte::page')

@section('title', 'Add Schoology Credentials')

@section('content_header')
<div class="container mt-5" style="max-width: 50%;">

    <h2 class="text-center mb-4">Add Schoology Credentials</h2>

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

    <form action="{{ route('schoology-credentials.store') }}" method="POST" class="p-4 border rounded shadow-sm">
        @csrf

        <div class="form-group mb-3">
            <label for="school_id" class="form-label">Student ID</label>
            <input type="text" name="school_id" id="school_id" class="form-control form-control-lg" value="{{ old('school_id') }}" required>
        </div>

        <div class="form-group mb-4">
            <label for="schoology_credentials" class="form-label">Schoology Credentials</label>
            <textarea name="schoology_credentials" id="schoology_credentials" class="form-control form-control-lg" rows="1" required>{{ old('schoology_credentials') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Credentials</button>
    </form>
</div>
@endsection
