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
                                <td>{{ $student->status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal" 
                                       data-id="{{ $student->id }}"
                                       data-school_id="{{ $student->school_id }}"
                                       data-lastname="{{ $student->lastname }}"
                                       data-firstname="{{ $student->firstname }}"
                                       data-middlename="{{ $student->middlename }}"
                                       data-course="{{ $student->course->name }}"
                                       data-birthday="{{ $student->birthday }}"
                                       data-status="{{ $student->status }}">
                                       Edit
                                    </a>
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

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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

                        @if (session('download_skipped'))
                            <div class="mt-3">
                                <a href="{{ session('download_skipped') }}" class="btn btn-outline-secondary btn-sm" download>
                                    ⬇️ Download Skipped Students CSV
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bootstrap Pagination Links -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center mt-5">
                        @if ($students->onFirstPage())
                            <li class="page-item disabled"><span class="page-link" aria-disabled="true">&laquo;</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev" aria-label="Previous">&laquo;</a></li>
                        @endif

                        @foreach ($students->links()->elements[0] as $page => $url)
                            <li class="page-item {{ $students->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        @if ($students->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next" aria-label="Next">&raquo;</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link" aria-disabled="true">&raquo;</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .pagination { margin-top: 20px; }
        .pagination > .page-item > .page-link { padding: 8px 16px; font-size: 14px; }
        .pagination > .page-item > .page-link svg { width: 1em; height: 1em; vertical-align: middle; }
        .pagination > .page-item > .page-link span { font-size: 1em; }
        .table td, .table th { padding: 0.4rem; vertical-align: middle; }
        .table-bordered { border: 1px solid #dee2e6; }
        .table-hover tbody tr:hover { background-color: #f5f5f5; }
        .thead-dark th { background-color: #343a40; color: white; }
    </style>
@endpush

@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#student-table').DataTable({
                paging: false,
                lengthChange: false,
                searching: false,
                ordering: true,
                info: false,
                autoWidth: false,
                responsive: true
            });

            $('#editStudentModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#editStudentId').val(button.data('id'));
                modal.find('#editSchoolId').val(button.data('school_id'));
                modal.find('#editLastname').val(button.data('lastname'));
                modal.find('#editFirstname').val(button.data('firstname'));
                modal.find('#editMiddlename').val(button.data('middlename'));
                modal.find('#editCourse').val(button.data('course'));
                modal.find('#editBirthday').val(button.data('birthday'));
                modal.find('#editStatus').val(button.data('status'));
            });

            window.confirmDelete = function () {
                return confirm('Are you sure you want to delete this student? This action cannot be undone.');
            };
        });
    </script>
@endpush

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editStudentForm" action="{{ url('student/update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editStudentId">
                    <div class="mb-3">
                        <label for="editSchoolId" class="form-label">School ID</label>
                        <input type="text" class="form-control" id="editSchoolId" name="school_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="editLastname" class="form-label">Lastname</label>
                        <input type="text" class="form-control" id="editLastname" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFirstname" class="form-label">Firstname</label>
                        <input type="text" class="form-control" id="editFirstname" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editMiddlename" class="form-label">Middlename</label>
                        <input type="text" class="form-control" id="editMiddlename" name="middlename">
                    </div>
                    <div class="mb-3">
                        <label for="editCourse" class="form-label">Course</label>
                        <input type="text" class="form-control" id="editCourse" name="course" required>
                    </div>
                    <div class="mb-3">
                        <label for="editBirthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="editBirthday" name="birthday" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
