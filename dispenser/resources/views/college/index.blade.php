{{-- <!DOCTYPE html>
<html>
<head>
    <title>College Import</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
@extends('adminlte::page')
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 mt-5">
<!-- added top -->
<h3>College table</h3>
<hr>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                        <tbody>

                        @foreach ($colleges as $item )
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->code}}</td>
                                <td>{{$item->name}}</td>
                                <!-- <td>{{$item->status}}</td> -->
                                <td>
                                @if ($item->status == 1)
                                Active
                            @else
                                Inactive
                            @endif


                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>

<!-- added from below -->
                @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card-header">
                    <h4>Import to database</h4>
                </div>
                <div class="card-body mt-2">
                    <form action="{{ url('college/import')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                            <input type="file" name="import_file" class="form-control" />
                            <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                <!-- //Show Tables -->
                
                <!-- New -->
                 </div>
            </div>
        </div>
    </div>
</body>
</html> --}}

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
@endsection
