{{-- resources/views/satpaccount/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Add SATP User')

@section('content_header')
@endsection

@section('content')
<div class="container mt-5" style="max-width: 50%;">
    <h2 class="text-center mb-4">Add New SATP User</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('satpaccount.store') }}" method="POST" class="p-4 border rounded shadow-sm">
        @csrf
        <div class="form-group mb-3">
            <label for="school_id" class="form-label">Student ID</label>
            <input type="text" name="school_id" id="school_id" class="form-control" value="{{ old('school_id') }}" required>
        </div>

        <div class="form-group mb-4">
            <label for="satp_password" class="form-label">SATP Password</label>
            <input type="text" name="satp_password" id="satp_password" class="form-control" value="{{ old('satp_password') }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add SATP Account</button>
    </form>
</div>
@endsection
