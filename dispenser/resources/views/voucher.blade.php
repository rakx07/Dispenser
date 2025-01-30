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
        .student-info, .satp-box {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-top: 15px;
        }
        .student-info h3, .satp-box h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }
        .done-button {
            margin-top: 20px;
        }
        .content-container {
            border: 2px solid #ccc; /* Add border around the entire content */
            border-radius: 8px;
            padding: 15px;
            background-color: #f4f4f4;
        }
        .row {
            margin-top: 30px;
        }
        .col-md-7, .col-md-5 {
            padding-left: 15px;
            padding-right: 15px;
            display: flex;
            flex-direction: column;
        }
        .col-md-7 {
            flex-grow: 1;
        }
        .col-md-5 {
            flex-grow: 1;
            display: flex;
            justify-content: flex-start;
        }
        .satp-box, .student-info {
            flex-grow: 1;
        }
        .col-md-5 .satp-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .alert {
            margin-top: 20px;
        }
        /* Tighten up the spacing between elements inside satp-box */
        .satp-box p {
            margin-bottom: 5px;
            line-height: 1.4; /* Reduce line height */
        }
        .satp-box button {
            padding: 2px 6px;
            font-size: 0.85rem;
            margin-left: 10px;
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
            <!-- Student Information Column -->
            <div class="col-md-7">
                <div class="student-info">
                @if (isset($student))
                    <h3>Name: {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}</h3>
                    <div class="satp-box">
                        <p><strong>ID Number: </strong>{{ $student->school_id }}</p>
                        <p><strong>Course:</strong> {{ $student->course->name }}</p>
                        <p><strong>Email: </strong> {{ isset($email) ? $email : 'Not Available' }}</p>
                        <p><strong>Password: </strong> 
                        <span id="password-field"> 
                           {{ isset($password) ? str_repeat('*', strlen($password)) : 'Not Available' }}
                         </span> 
                        <button type="button" id="toggle-password" class="btn btn-sm btn-outline-primary">Show</button>
                    </p>
                    </div>

                    <!-- Voucher Code Box -->
                    <div class="satp-box">
                        <p><strong>Voucher Code:</strong> 
                            <span id="voucher-code-field">
                                {{ str_repeat('*', strlen($voucher->voucher_code ?? '')) }}
                            </span> 
                            <button type="button" id="toggle-voucher-code" class="btn btn-sm btn-outline-primary">Show</button>
                        </p>
                    </div>
                @else
                    <div class="alert alert-danger">
                        Student information not found.
                    </div>
                @endif
                </div>
            </div>

            <!-- SATP Credentials Column -->
            <div class="col-md-5">
                @if (isset($student))
                    <div class="satp-box">
                        <h3>SATP Credentials</h3>
                        <p><strong>SATP Username:</strong> {{ $student->school_id }}</p>
                        <p><strong>SATP Password:</strong> 
                            <span id="satp-password-field">
                                {{ str_repeat('*', strlen($satp_password)) }}
                            </span> 
                            <button type="button" id="toggle-satp-password" class="btn btn-sm btn-outline-primary">Show</button>
                        </p>
                        <p><strong>SATP Link:</strong> <span>http://satp.ndmu.edu.ph</span></p>
                        <p style="color: red; font-weight: bold;"><strong>NOTE: <span>Use Google Chrome Web Browser.</span></strong></p>
                    </div>
                @endif
            </div>
        </div>
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

    <!-- JavaScript to Toggle Password and Voucher Code Visibility -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle password visibility
            const passwordField = document.getElementById('password-field');
            const togglePasswordButton = document.getElementById('toggle-password');
            let isPasswordHidden = true;

            togglePasswordButton.addEventListener('click', function () {
                if (isPasswordHidden) {
                    passwordField.textContent = "{{ isset($password) ? $password : 'Not Available' }}";
                    togglePasswordButton.textContent = "Hide";
                } else {
                    passwordField.textContent = "{{ isset($password) ? str_repeat('*', strlen($password)) : 'Not Available' }}";
                    togglePasswordButton.textContent = "Show";
                }
                isPasswordHidden = !isPasswordHidden;
            });

            // Toggle SATP password visibility
            const satpPasswordField = document.getElementById('satp-password-field');
            const toggleSatpPasswordButton = document.getElementById('toggle-satp-password');
            let isSatpPasswordHidden = true;

            toggleSatpPasswordButton.addEventListener('click', function () {
                if (isSatpPasswordHidden) {
                    satpPasswordField.textContent = "{{ $satp_password }}";
                    toggleSatpPasswordButton.textContent = "Hide";
                } else {
                    satpPasswordField.textContent = "{{ isset($satp_password) ? str_repeat('*', strlen($satp_password)) : 'Not Available' }}";
                    toggleSatpPasswordButton.textContent = "Show";
                }
                isSatpPasswordHidden = !isSatpPasswordHidden;
            });

            // Toggle voucher code visibility
            const voucherCodeField = document.getElementById('voucher-code-field');
            const toggleVoucherCodeButton = document.getElementById('toggle-voucher-code');
            let isVoucherCodeHidden = true;

            toggleVoucherCodeButton.addEventListener('click', function () {
                if (isVoucherCodeHidden) {
                    voucherCodeField.textContent = "{{ $voucher->voucher_code ?? 'Not available' }}";
                    toggleVoucherCodeButton.textContent = "Hide";
                } else {
                    voucherCodeField.textContent = "{{ isset($voucher->voucher_code) ? str_repeat('*', strlen($voucher->voucher_code)) : 'Not available' }}";
                    toggleVoucherCodeButton.textContent = "Show";
                }
                isVoucherCodeHidden = !isVoucherCodeHidden;
            });
        });
    </script>
</body>
</html>
