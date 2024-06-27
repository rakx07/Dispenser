@extends('adminlte::page')

@section('title', 'Edit College')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mt-5">
                <h3>Edit College</h3>
                <hr>
                <form action="{{ url('college/update/' . $college->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $college->code }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $college->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $college->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $college->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
