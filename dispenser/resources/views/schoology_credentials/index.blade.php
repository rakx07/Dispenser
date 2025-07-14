@extends('adminlte::page')

@section('title', 'Import Schoology Credentials')

@section('content_header')
    <h1>Import Schoology Credentials</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(session('skipped'))
            <div class="alert alert-warning">Skipped {{ session('skipped') }} duplicate record(s).</div>
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

        <form action="{{ url('schoology-credentials/import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="import_file">Upload Excel File (.xlsx)</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="import_file" class="custom-file-input" id="import_file" required onchange="updateFileName()">
                        <label class="custom-file-label" for="import_file">Choose file</label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('js')
<script>
    function updateFileName() {
        const input = document.getElementById('import_file');
        const label = input.nextElementSibling;
        const fileName = input.files[0] ? input.files[0].name : 'Choose file';
        label.innerText = fileName;
    }
</script>
@endsection
@endsection
