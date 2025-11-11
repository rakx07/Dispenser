@extends('adminlte::page')

@section('title', 'Student Transactions')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Student Transactions</h2>
        <a href="{{ route('audit.transactions.export') }}" class="btn btn-success">
            Download Excel
        </a>
    </div>

    <div class="mb-3">
        <p class="mb-2">Total Transactions: <strong>{{ $totalTransactions }}</strong></p>
        <input type="text" id="search" class="form-control" placeholder="Search by ID, name or course...">
    </div>

    {{-- AJAX Search Results --}}
    <div id="search-results">
        @include('audit.partials.transaction_table', ['transactions' => $transactions])
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function() {
    // CSRF setup for all AJAX (needed for POST to /record)
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    });

    // Live search
    $('#search').on('keyup', function () {
        let query = $(this).val();
        $.ajax({
            url: "{{ route('transactions.search') }}",
            type: "GET",
            data: { query },
            success: function (html) { $('#search-results').html(html); },
            error: function () { $('#search-results').html('<p class="text-danger">Error loading results.</p>'); }
        });
    });
})();
</script>
@endsection
