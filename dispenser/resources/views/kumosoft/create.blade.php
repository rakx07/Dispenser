@extends('adminlte::page')

@section('title', 'Add Kumosoft Credentials')

@section('content_header')
    <!-- Additional header content can be added here -->
@endsection

@section('content')
<div class="card">
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="card-header">
                <h4>Manually Add Kumosoft Credentials</h4>
            </div>
            <div class="card-body">
                <!-- Display success message -->
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kumosoft.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="school_id">Student ID</label>
                        <input type="text" name="school_id" id="school_id" class="form-control" value="{{ old('school_id') }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="kumosoft_credentials">Kumosoft Credentials</label>
                        <input type="text" name="kumosoft_credentials" id="kumosoft_credentials" class="form-control" value="{{ old('kumosoft_credentials') }}" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save Credentials</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
