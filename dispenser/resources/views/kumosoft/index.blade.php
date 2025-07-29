@extends('adminlte::page')

@section('title', 'Kumosoft Import')

@section('content_header')
    <!-- Additional header content can be added here -->
@endsection

@section('content')
<div class="card">
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="card-header">
                <h4>Import Kumosoft Excel Data to Database</h4>
            </div>
            <div class="card-body">
                <!-- Display success message if exists -->
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('skipped'))
                    <div class="alert alert-warning">
                        Skipped entries during import: {{ session('skipped') }}
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

                <form action="{{ url('kumosoft/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="import_file">Choose Excel File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="import_file" class="custom-file-input" id="import_file" required onchange="updateFileName()">
                                <label class="custom-file-label" for="import_file">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function updateFileName() {
        const input = document.getElementById('import_file');
        const label = input.nextElementSibling; // The label element
        const fileName = input.files[0] ? input.files[0].name : 'Choose file';
        label.innerText = fileName;
    }
</script>
@endsection
