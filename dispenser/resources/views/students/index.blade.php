@extends('adminlte::page')

@section('title', 'Student Import')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                <h3>Student Table</h3>
                <hr>
                <table id="student-table" class="table table-bordered table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>School ID</th>
                            <th>Lastname</th>
                            <th>Firstname</th>
                            <th>Middlename</th>
                            <th>Course</th>
                            <th>Birthday</th>
                            <th>Voucher</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>{{ $student->school_id }}</td>
                                <td>{{ $student->lastname }}</td>
                                <td>{{ $student->firstname }}</td>
                                <td>{{ $student->middlename }}</td>
                                <td>{{ $student->course->name }}</td>
                                <td>{{ $student->birthday }}</td>
                                <td>{{ $student->voucher_id }}</td>
                                <td>{{ $student->email_id }}</td>
                                <td>{{ $student->status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="{{ url('student/edit/' . $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ url('student/delete/' . $student->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">
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

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Import to Database</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('students/import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="import_file" class="form-control" />
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bootstrap Pagination Links -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center mt-5">
                        {{-- Previous Page Link --}}
                        @if ($students->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-disabled="true">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($students->links()->elements[0] as $page => $url)
                            <li class="page-item {{ $students->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($students->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-disabled="true">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        /* Custom styles for pagination */
        .pagination {
            margin-top: 20px; /* Adjust margin as needed */
        }
        .pagination > .page-item > .page-link {
            padding: 8px 16px; /* Adjust padding as needed */
            font-size: 14px; /* Adjust font size as needed */
        }
        /* Adjust SVG icon size */
        .pagination > .page-item > .page-link svg {
            width: 1em; /* Set desired width */
            height: 1em; /* Set desired height */
            vertical-align: middle; /* Align vertically */
        }
        /* Adjust icon size for Previous and Next arrows */
        .pagination > .page-item > .page-link span {
            font-size: 1em; /* Adjust font size as needed */
        }
        /* Custom styles for table cell height */
        .table td,
        .table th {
            padding: 0.4rem; /* Adjust padding to make cells smaller */
            vertical-align: middle; /* Align content vertically */
        }
        /* Add border and hover effects */
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .thead-dark th {
            background-color: #343a40;
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#student-table').DataTable({
                paging: false, // Disable front-end pagination (handled by Laravel)
                lengthChange: false, // Disable length change
                searching: false, // Disable search feature
                ordering: true, // Enable ordering (sorting)
                info: false, // Disable info display (handled by Laravel pagination)
                autoWidth: false, // Disable auto width calculation
                responsive: true // Enable responsiveness
            });
        });

        function confirmDelete() {
            return confirm('Are you sure you want to delete this student? This action cannot be undone.');
        }
    </script>
@endpush
