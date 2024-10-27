@extends('adminlte::page')

@section('title', 'Student Import')

@section('content_header')
    <!-- Additional header content can be added here -->
@endsection

@section('content')
<div class="card">
    <div class="row">
        <div class="col-md-12 mt-5">
            <div class="card-header">
                <h4>Import SATP Excel Data to Database</h4>
            </div>
            <div class="card-body">
                <!-- Display success message if exists -->
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ url('satpaccount/import') }}" method="POST" enctype="multipart/form-data">
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
@endsection