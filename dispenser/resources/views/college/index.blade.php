@extends('adminlte::page')

@section('title', 'College Import')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mt-5">
                <h3>College Table</h3>
                <hr>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($colleges as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('college/edit/' . $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ url('college/delete/' . $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4>Import to Database</h4>
                    </div>
                    <div class="card-body mt-2">
                        <form action="{{ url('college/import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="import_file" class="form-control" />
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this college? This action cannot be undone.');
        }
    </script>
@endsection
