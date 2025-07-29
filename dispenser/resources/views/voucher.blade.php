<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
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
            font-weight: bold;
        }
        .content-container,
        .student-info,
        .satp-box {
            border: 3px solid #444;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
            font-size: 1.15rem;
            font-weight: bold;
        }
        h4, h3, h1, p, a {
            font-weight: bold !important;
        }
        h1, h3, h4 {
            font-size: 1.6rem;
        }
        .done-button {
            margin-top: 20px;
        }
        .red-note {
            color: red;
            font-weight: bold;
        }
        .btn-show-all {
            display: block;
            margin: 20px auto 0 auto;
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
        <h1 class="mb-4 text-center"><b>Student WiFi & System Credentials</b></h1>

        <!-- Center Top Box - Name and Course -->
        <div class="content-container text-center mb-4">
            @if (isset($student))
                <h3><strong>Name:</strong> {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}</h3>
                <p><strong>Course:</strong> {{ $student->course->name }}</p>
                <p><strong>WiFi Voucher:</strong> <span id="voucher-code-field">********</span></p>
            @else
                <div class="alert alert-danger">Student information not found.</div>
            @endif
        </div>

        <!-- Grid of 4 Credential Boxes -->
        <div class="row g-4">
            <!-- Left Top - Email Credentials -->
            <div class="col-md-6">
                <div class="student-info h-100">
                    <h4>Email Credentials</h4>
                    <p><strong>Email:</strong> {{ $email ?? 'Not Available' }}</p>
                    <p><strong>Password:</strong> <span id="password-field">********</span></p>
                </div>
            </div>

            <!-- Right Top - Kumosoft Credentials -->
            <div class="col-md-6">
                <div class="student-info h-100">
                    <h4>Kumosoft Credentials</h4>
                    <p><strong>Kumosoft Username:</strong> {{ $student->school_id ?? 'Not Available' }}</p>
                    <p><strong>Kumosoft Password:</strong> <span id="kumosoft-password">********</span></p>
                </div>
            </div>

            <!-- Left Bottom - Schoology -->
            <div class="col-md-6">
                <div class="student-info h-100">
                    <h4>Schoology Access</h4>
                    <p><strong>Username:</strong> {{ $student->school_id }}</p>
                    <p><strong>Password:</strong> <span id="password-field-schoology">********</span></p>
                    <p><strong>Schoology Link:</strong> https://ndmu.schoology.com/</p>
                </div>
            </div>

            <!-- Right Bottom - SATP -->
            <div class="col-md-6">
                <div class="satp-box h-100">
                    <h4>SATP Credentials</h4>
                    <p><strong>SATP Username:</strong> {{ $student->school_id }}</p>
                    <p><strong>SATP Password:</strong> <span id="satp-password-field">********</span></p>
                    <p><strong>SATP Link:</strong> http://satp.ndmu.edu.ph</p>
                    <p class="red-note">NOTE: Use Google Chrome Web Browser.</p>
                </div>
            </div>
        </div>

        <!-- Show All Button -->
        <button id="toggle-all" class="btn btn-primary btn-show-all">Show All</button>

        <!-- Done Button -->
        <div class="text-center done-button">
            <button id="doneButton" class="btn btn-primary btn-lg">Done</button>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <!-- Local JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let transactionRecorded = false;

        document.getElementById('toggle-all').addEventListener('click', function () {
            if (!transactionRecorded) {
                Swal.fire({
                    title: "Confirmation Required",
                    text: "By clicking 'Show All', you confirm that you have received your credentials. This action will be recorded.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        revealCredentials();
                        recordTransaction();
                        transactionRecorded = true;
                    }
                });
            } else {
                revealCredentials();
            }
        });

        function revealCredentials() {
            document.getElementById('password-field').textContent = "{{ $password ?? 'Not Available' }}";
            document.getElementById('voucher-code-field').textContent = "{{ $voucher->voucher_code ?? 'Not Available' }}";
            document.getElementById('satp-password-field').textContent = "{{ $satp_password ?? 'Not Available' }}";
            document.getElementById('password-field-schoology').textContent = "{{ $schoology_credentials ?? 'Not Available' }}";
            document.getElementById('kumosoft-password').textContent = "{{ $kumosoft_credentials ?? 'Not Available' }}";
        }

        function recordTransaction() {
            fetch("{{ route('transactions.recordShowPassword') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    student_id: "{{ $student->id }}"
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      console.log("Transaction Recorded:", data);
                  } else {
                      console.log("Transaction already exists, no new record.");
                  }
              })
              .catch(error => console.error("Error:", error));
        }

        document.getElementById('doneButton').addEventListener('click', function () {
            Swal.fire({
                icon: 'success',
                title: 'Thank you!',
                text: 'Have a nice day!',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "{{ route('welcome') }}";
            });
        });

        // 40-second inactivity timer
        let inactivityTimer;
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                window.location.href = "{{ route('welcome') }}";
            }, 40000);
        }

        ['mousemove', 'keydown', 'click', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        resetInactivityTimer();
    });
    </script>
</body>
</html>
