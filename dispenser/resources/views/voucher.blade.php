<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Local SweetAlert2 CSS -->
    <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
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
        .student-info {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .student-info h2 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .done-button {
            margin-top: 20px;
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
    <h1 class="mb-4"><b>Student Information</b></h1>

    <!-- Student Information -->
    <div class="student-info">
        
        @if (isset($student))
            <h3>Name: {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}</h3>
            <div class="satp-box border p-3 my-4 rounded">
            <p><strong>ID Number: </strong>{{ $student->school_id }}</p>
            <p><strong>Course:</strong> {{ $student->course->name }}</p>
            <p><strong>Email:</strong> {{ ucfirst(Str::camel($student->email_id)) }}TBA</p>
            </div>
            <!-- New SATP Information Box -->
            <div class="satp-box border p-3 my-4 rounded">
                <h3>SATP Credentials</h3><br>
                <p><strong>SATP Username:</strong> {{ $student->school_id }}</p>
                <p><strong>SATP Password:</strong> {{ $satp_password }}</p>
                <p><strong>SATP Link:</strong> <span>http://satp.ndmu.edu.ph</span></p>


            </div>
            <div class="satp-box border p-3 my-4 rounded">
            <p><strong>Voucher Code:</strong> {{ $voucher->voucher_code ?? 'Not available' }}</p>
            </div>
        @else
            <div class="alert alert-danger">
                Student information not found.
            </div>
        @endif
    </div>

    <!-- Done Button -->
    <div class="text-center done-button">
        <button id="doneButton" class="btn btn-primary btn-lg">Done</button>
    </div>
</main>

    
    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <!-- Local Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Local SweetAlert2 JS -->
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                window.location.href = "{{ route('welcome') }}";
            }, 60000); // 60 seconds timeout

            document.getElementById('doneButton').addEventListener('click', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Thank you!',
                    text: 'Have a nice day!',
                    showConfirmButton: false,
                    timer: 2000 // 2 seconds
                }).then(() => {
                    window.location.href = "{{ route('welcome') }}"; // Redirect to welcome page
                });
            });
        });
    </script>

    <!-- Check if SweetAlert2 is correctly initialized -->
    <script>
        console.log(Swal); // Should log the Swal object if correctly loaded
    </script>
</body>
</html>
