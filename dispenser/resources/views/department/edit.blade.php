
@extends('adminlte::page')

@section('title', 'Edit Department')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mt-5">
                <h3>Edit Department</h3>
                <hr>
                <form action="{{ url('department/update/' . $department->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $department->code }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="college_id">College</label>
                        <select name="college_id" class="form-control" required>
                            @foreach($colleges as $college)
                                <option value="{{ $college->id }}" {{ $department->college_id == $college->name ? 'selected' : '' }}>
                                    {{ $college->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $department->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $department->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection

