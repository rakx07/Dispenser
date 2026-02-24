@extends('adminlte::page')

@section('title', 'Kumosoft Import')

@section('content_header')
@endsection

@section('content')
<div class="card mt-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="mb-0">Import Kumosoft Excel Data to Database</h4>

        <a href="{{ route('kumosoft.template.download') }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> Download Excel Template
        </a>
    </div>

    <div class="card-body">

        {{-- =========================
             SUCCESS / ERROR MESSAGES
        ========================== --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif


        {{-- =========================
             IMPORT SUMMARY
        ========================== --}}
        @if(session('kumosoft_import_stats'))
            @php $s = session('kumosoft_import_stats'); @endphp

            <div class="alert alert-info">
                <h5 class="mb-2"><b>Import Summary</b></h5>
                <div class="row">
                    <div class="col-md-4"><b>Total Rows:</b> {{ $s['total_rows'] }}</div>
                    <div class="col-md-4"><b>Processed Rows:</b> {{ $s['kept_rows'] }}</div>
                    <div class="col-md-4"><b>Failed:</b> {{ $s['failed'] }}</div>

                    <div class="col-md-4 mt-2"><b>Matched by ID:</b> {{ $s['matched_by_id'] }}</div>
                    <div class="col-md-4 mt-2"><b>Matched by Name:</b> {{ $s['matched_by_name'] }}</div>
                    <div class="col-md-4 mt-2"><b>Duplicates in Upload:</b> {{ $s['duplicates_in_upload'] }}</div>
                </div>

                @if(session('kumosoft_failed_file'))
                    @php $file = basename(session('kumosoft_failed_file')); @endphp
                    <div class="mt-3">
                        <a class="btn btn-danger btn-sm"
                           href="{{ route('kumosoft.failed.download', ['filename' => $file]) }}">
                            <i class="fas fa-download"></i> Download Failed Rows Report
                        </a>
                    </div>
                @endif
            </div>
        @endif


        {{-- =========================
             VALIDATION ERRORS
        ========================== --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <b>Please fix the following:</b>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- =========================
             IMPORT FORM
        ========================== --}}
        <form action="{{ url('kumosoft/import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="file">Choose Excel File</label>

                <div class="input-group">
                    <div class="custom-file">
                        <input type="file"
                               name="file"
                               class="custom-file-input"
                               id="file"
                               required
                               onchange="updateFileName()">

                        <label class="custom-file-label" for="file">Choose file</label>
                    </div>

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </div>
                </div>

                <small class="text-muted">
                    Accepted formats: .xlsx, .xls, .csv
                </small>
            </div>
        </form>

    </div>
</div>
@endsection

@section('js')
<script>
    function updateFileName() {
        const input = document.getElementById('file');
        const label = input.nextElementSibling;
        const fileName = input.files && input.files[0] ? input.files[0].name : 'Choose file';
        label.innerText = fileName;
    }
</script>
@endsection