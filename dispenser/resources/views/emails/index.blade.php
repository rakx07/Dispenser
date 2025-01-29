@extends('adminlte::page')

@section('title', 'Import Emails')

@section('content_header')
    {{-- <h1>Import Emails</h1> --}}
@endsection

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Import Emails</h1>

        <!-- Display success or error messages -->
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- File upload form -->
        <form action="{{ route('emails.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="import_file" class="form-label">Upload Excel File</label>
                <input type="file" name="import_file" id="import_file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
@endsection
