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
        .content-container {
            border: 2px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .student-info, .satp-box {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-top: 15px;
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
            margin: 10px auto;
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

        <div class="content-container">
            <div class="row">
                <!-- Left Side - Student Information -->
                <div class="col-md-7">
                    <div class="student-info">
                        @if (isset($student))
                            <h3>Name: {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}</h3>
                            <p><strong>ID Number:</strong> {{ $student->school_id }}</p>
                            <p><strong>Course:</strong> {{ $student->course->name }}</p>
                            <p><strong>Email:</strong> {{ $email ?? 'Not Available' }}</p>
                            <p><strong>Password:</strong>
                                <span id="password-field">********</span>
                            </p>
                        @else
                            <div class="alert alert-danger">
                                Student information not found.
                            </div>
                        @endif
                    </div>

                    <!-- Voucher Code in Separate Div -->
                    <div class="student-info mt-3">
                        <p><strong>Voucher Code:</strong>
                            <span id="voucher-code-field">********</span>
                        </p>
                    </div>
                </div>

                <!-- Right Side - SATP Credentials -->
                <div class="col-md-5">
                    @if (isset($student))
                        <div class="satp-box">
                            <h3>SATP Credentials</h3>
                            <p><strong>SATP Username:</strong> {{ $student->school_id }}</p>
                            <p><strong>SATP Password:</strong>
                                <span id="satp-password-field">********</span>
                            </p>
                            <p><strong>SATP Link:</strong> <span>http://satp.ndmu.edu.ph</span></p>
                            <p class="red-note"><strong>NOTE: Use Google Chrome Web Browser.</strong></p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Single Show All Button (Inside the Content Box) -->
            <button id="toggle-all" class="btn btn-primary btn-show-all">Show All</button>
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
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let transactionRecorded = false; // Ensures it is recorded only once

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
                        recordTransaction(); // Record only the first time
                        transactionRecorded = true; // Prevent re-recording
                    }
                });
            } else {
                revealCredentials(); // Just reveal credentials without re-recording
            }
        });

        function revealCredentials() {
            document.getElementById('password-field').textContent = "{{ $password ?? 'Not Available' }}";
            document.getElementById('voucher-code-field').textContent = "{{ $voucher->voucher_code ?? 'Not Available' }}";
            document.getElementById('satp-password-field').textContent = "{{ $satp_password ?? 'Not Available' }}";
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
    });
</script>


</body>
</html>
