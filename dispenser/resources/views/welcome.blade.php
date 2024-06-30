<!-- resources/views/signin.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        footer {
            background-color: darkgreen;
            color: white;
            text-align: center;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top" style="background-color: darkgreen">
        <div class="container-fluid">
            <a class="navbar-brand fs-7 text-white">
                <img src="{{ asset('ndmulogo.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" />
                <b>Notre Dame of Marbel University</b>
            </a>
            <span class="navbar-text text-white">
               Voucher Account Allocator
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-5">
        <h1 class="mb-4"><b>Student Information</b></h1>

        @if (session('message'))
            <div class="alert alert-info">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('check.student') }}">
            @csrf
            <div class="mb-3">
                <label for="courseSelect" class="form-label"><b>Select Your Course</b></label>
                <select class="form-select" id="courseSelect" name="courseSelect">
                    <option value=""><small>Select your course...</small></option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->code }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="idNumber" class="form-label"><b>ID Number</b></label>
                <input type="text" name="idNumber" placeholder="Enter your ID number" class="form-control form-control-sm" id="idNumber" style="width: 200px;">
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label"><b>Last Name</b></label>
                <input type="text" name="lastname" placeholder="Enter your Last name" class="form-control form-control-sm" id="lastname" style="width: 200px;">
            </div>
            <div class="mb-3">
                <label for="birthday" class="form-label"><b>Birthday<small> (e.g. 2001-01-29) </small></b></label>
                <input type="text" name="birthday" placeholder="Enter your birthday" class="form-control form-control-sm" id="birthday" style="width: 200px;">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </main>

    <!-- Account Creation Modal -->
    <div class="modal fade" id="accountCreationModal" tabindex="-1" aria-labelledby="accountCreationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountCreationModalLabel">Create Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="accountCreationForm" method="POST" action="{{ route('create.student.account') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="schoolId" class="form-label">School ID</label>
                            <input type="text" class="form-control" id="schoolId" name="schoolId" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        @if(session('showModal'))
            var myModal = new bootstrap.Modal(document.getElementById('accountCreationModal'), {
                keyboard: false
            });
            document.getElementById('schoolId').value = '{{ session('school_id') }}';
            myModal.show();
        @endif

        // Show success message and redirect after modal hidden
        $('#accountCreationModal').on('hidden.bs.modal', function () {
            // Alert dialog box
            alert('Student user account created successfully!');
            // Redirect to voucher page
            window.location.href = "{{ route('signin') }}";
        });
    </script>
</body>
</html>
