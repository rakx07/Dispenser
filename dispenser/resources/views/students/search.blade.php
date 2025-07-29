@extends('adminlte::page')

@section('title', 'Search Student')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Search Student</h1>

    {{-- Download Excel Button --}}
    <a href="{{ route('students.export') }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Download Excel
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="input-group mb-3">
        <input type="text" id="student-search" class="form-control" placeholder="Search by ID or Name...">
    </div>

    {{-- AJAX Search Results --}}
    <div id="search-student-results">
        @include('students.partials.student_table', ['students' => $students])
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        padding: 0.75rem;
        height: 60px;
        white-space: nowrap;
    }
</style>
@endpush

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#student-search').on('keyup', function () {
        let query = $(this).val();

        $.ajax({
            url: "{{ route('students.search.ajax') }}",
            type: "GET",
            data: { query: query },
            success: function (data) {
                $('#search-student-results').html(data);
            },
            error: function () {
                $('#search-student-results').html('<p class="text-danger">Error loading results.</p>');
            }
        });
    });
</script>
@endsection
