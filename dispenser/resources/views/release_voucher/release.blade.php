{{-- resources/views/release_voucher/release.blade.php --}}
@extends('adminlte::page')

@section('title', 'Release Voucher')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <h3>Release Voucher</h3>
                <hr>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container">

        {{-- Status message --}}
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            
            {{-- LEFT SIDE: Generate voucher --}}
            <div class="col-md-4">
                <div class="card mt-2">
                    <div class="card-header">
                        <h4>Generate Voucher(s)</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('release-voucher.generate') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Number of vouchers to generate</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                                       value="{{ old('quantity', 1) }}">
                                <small class="text-muted">Enter any amount (minimum 1).</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Get Voucher(s)</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: Voucher list --}}
            <div class="col-md-8">
                @if(isset($generatedVouchers) && $generatedVouchers->count() > 0)
                    <div class="card mt-2">
                        <div class="card-header">
                            <h4 class="mb-0">Voucher(s) to be Released</h4>
                        </div>

                        <div class="card-body">
                            
                            <p class="text-muted">
                                Once released, they will be marked as <strong>is_given = 1</strong> and cannot be reused.
                            </p>

                            <form action="{{ route('release-voucher.release') }}" method="POST">
                                @csrf

                                <div class="table-responsive">
                                    <table id="release-table" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Voucher Code</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($generatedVouchers as $index => $voucher)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $voucher->id }}</td>
                                                <td>{{ $voucher->voucher_code }}</td>
                                                <td>
                                                    @if ($voucher->is_given)
                                                        <span class="badge bg-danger">Given</span>
                                                    @else
                                                        <span class="badge bg-success">Available</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            {{-- hidden to pass for releasing --}}
                                            <input type="hidden" name="voucher_ids[]" value="{{ $voucher->id }}">
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">
                                        Release Voucher(s)
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                @else
                    <div class="alert alert-info mt-2">
                        No vouchers generated yet.
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#release-table').DataTable();
    });
</script>
@endpush
