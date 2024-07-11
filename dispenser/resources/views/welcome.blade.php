<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .large-selectpicker {
            width: 100% !important;
        }
        .bootstrap-select .dropdown-menu {
            max-height: 300px !important;
            overflow-y: auto !important;
            white-space: normal !important;
        }
        .bootstrap-select .dropdown-menu .inner {
            white-space: normal !important;
        }
        .bootstrap-select .dropdown-menu li {
            white-space: normal !important;
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

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
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

        @if (session('showModal'))
            <div class="alert alert-info">
                Student found with ID: {{ session('school_id') }}
            </div>
        @endif

        <form method="POST" action="{{ route('check.student') }}">
            @csrf
            <div class="mb-3">
                <label for="courseSelect" class="form-label"><b>Select Your Course</b></label>
                <div class="w-100 mt-2">
                    <select class="form-select selectpicker large-selectpicker" id="courseSelect" name="courseSelect" data-live-search="true">
                        <option value=""><small>Select your course...</small></option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->code }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
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
                <label for="birthday" class="form-label"><b>Birthday<small> (e.g. 2001-01-29 / yyyy-mm-dd) </small></b></label>
                <input type="text" name="birthday" placeholder="Enter your birthday" class="form-control form-control-sm" id="birthday" style="width: 200px;">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </main>

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('showModal'))
                Swal.fire({
                    icon: 'success',
                    title: 'Student Found',
                    text: 'Student found with ID: {{ session('school_id') }}',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = "{{ route('voucher') }}"; // Adjust this route as needed
                });
            @endif

            // Initialize Bootstrap-Select
            $('.selectpicker').selectpicker();
        });
    </script>
</body>
</html>
