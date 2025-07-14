@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Schoology Credentials</h3>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('schoology-credentials.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="school_id">School ID</label>
            <input type="text" name="school_id" id="school_id" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="schoology_credentials">Credentials</label>
            <textarea name="schoology_credentials" id="schoology_credentials" class="form-control" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3">Save</button>
    </form>
</div>
@endsection
