@extends('adminlte::page')

@section('title', 'Voucher Import')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <h3>Voucher Table</h3>
                <hr>
                <table id="voucher-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->id }}</td>
                                <td>{{ $voucher->voucher_code }}</td>
                                <td>{{ $voucher->is_given ? 'Taken' : 'Available' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $vouchers->links() }}

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Import to Database</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('voucher/import') }}" method="POST" enctype="multipart/form-data">
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

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#voucher-table').DataTable();
        });

        function confirmDelete() {
            return confirm('Are you sure you want to delete this voucher? This action cannot be undone.');
        }
    </script>
@endpush
