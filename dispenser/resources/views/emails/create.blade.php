@extends('adminlte::page')

@section('title', 'Add Email Entry')

@section('content_header')
<div class="container mt-5" style="max-width: 50%;">

    <h2 class="text-center mb-4">Add New Email Entry</h2>

    <!-- Display success message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Display validation and duplicate errors -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('emails.store') }}" method="POST" class="p-4 border rounded shadow-sm bg-light">
        @csrf

        <div class="mb-3">
            <label for="first_name" class="form-label fw-bold">First Name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label fw-bold">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email_address" class="form-label fw-bold">Email Address</label>
            <input type="email" name="email_address" class="form-control" value="{{ old('email_address') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Password</label>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>
        </div>

        <div class="mb-3">
            <label for="sch_id_number" class="form-label fw-bold">School ID Number</label>
            <input type="text" name="sch_id_number" class="form-control" value="{{ old('sch_id_number') }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Email Entry</button>
    </form>

</div>
@endsection
