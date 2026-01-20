<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Local Bootstrap Select CSS -->
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    
    <!-- Local SweetAlert2 JS -->
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
    
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
        .beating-text {
            color: red;
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            animation: beat 1s infinite;
        }
        @keyframes beat {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
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
            WiFi Access Code Manager
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-5">
        <!-- Beating Text -->
        <div class="mb-5">
            <p class="beating-text">GET YOUR INSTITUTIONAL EMAIL, SCHOOLOGY, SATP, AND WIFI VOUCHER CODE HERE!</p>
        </div>

        <div class="row">
            <div class="col-md-6">
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

                <form method="POST" action="{{ route('students.voucherAndSatp') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="courseSelect" class="form-label"><b>Select Your Course</b></label>
                        <div class="w-100 mt-2">
                            <select class="form-control selectpicker" id="courseSelect" name="courseSelect" data-live-search="true">
                                <option value=""><small>Select your course...</small></option>
                                @foreach ($courses as $course)
                                    <option data-subtext="{{ $course->code }}" value="{{ $course->code }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="idNumber" class="form-label"><b>ID Number</b></label>
                        <input type="text" name="idNumber" placeholder="Enter your ID number" class="form-control" id="idNumber">
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label"><b>Last Name</b></label>
                        <input type="text" name="lastname" placeholder="Enter your Last name" class="form-control" id="lastname">
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label"><b>Birthday<small> (e.g. 2001-01-29 / yyyy-mm-dd) </small></b></label>
                        <input type="text" name="birthday" placeholder="Enter your birthday" class="form-control" id="birthday">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" id="clearButton">Clear</button>
                </form>
            </div>
            <div class="col-md-6">
                <h1 class="mb-4"><b>Voucher Usage Guidelines</b></h1>
                <ul class="list-group">
                    <li class="list-group-item"><strong>• This voucher code is exclusively for enrolled students of Notre Dame of Marbel University.</strong></li>
                    <li class="list-group-item"><strong>• Each student is entitled to use one voucher code.</strong></li>
                    <li class="list-group-item"><strong>• The voucher code is valid for use with NDMUWLAN1, NDMUWDS, and similar WiFi access points.</strong></li>
                    <li class="list-group-item"><strong>• The voucher code provides 2 hours of access; the connection will be automatically disconnected after this time, requiring you to reconnect using the same voucher code.</strong></li>
                    <li class="list-group-item" style="color: red; font-weight: bold;">
                    <strong>• If there are concerns, kindly proceed to MIS Office.</strong>
                </ul>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <!-- Local Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Local jQuery (if required) -->
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    
    <!-- Local Bootstrap Select JS -->
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap-Select
            $('.selectpicker').selectpicker();
            
            @if(session('showModal'))
                Swal.fire({
                    icon: 'success',
                    title: 'Student Found!',
                    html: '<p>Student found with ID: <b>{{ session('school_id') }}</b></p>',
                    confirmButtonText: 'Great!',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                        window.location.href = "{{ route('voucher') }}"; // Adjust this route as needed
                    }
                });
            @endif

            // Clear button functionality
            document.getElementById('clearButton').addEventListener('click', function() {
                // Reset the form
                document.querySelector('form').reset();
                
                // Clear the selectpicker
                $('#courseSelect').selectpicker('val', '');
            });
        });
    </script>
</body>
</html>
