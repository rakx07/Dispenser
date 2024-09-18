<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11"> -->

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
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
            <h3>Name: {{ ucfirst($student->firstname) }} {{ ucfirst($student->middlename[0]) }}. {{ ucfirst($student->lastname) }}</h3>

            <p><strong>ID Number: </strong>{{ $student->school_id }}</p>
            
            <p><strong>Course:</strong> {{ ($student->course->name) }}</p>
            
            <p><strong>Email:</strong> {{ ucfirst(Str::camel($student->email_id)) }}TBA</p>
            
            <p><strong>Temporary Email Password:</strong> TBA </p>
            
            <p><strong>Voucher Code:</strong> {{ $voucher->voucher_code }}</p>
            
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

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Local SweetAlert2 JS -->
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                window.location.href = "{{ route('welcome') }}";
            }, 60000); // 90 seconds timeout

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
</body>
<script type="text/javascript">
    (function (global) {
        if (typeof (global) === "undefined") {
            throw new Error("window is undefined");
        }

        var _hash = "!";
        var noBackPlease = function () {
            global.location.href += "#";

            // Ensures the back button doesn't work.
            global.setTimeout(function () {
                global.location.href += "!";
            }, 50);
        };

        global.onhashchange = function () {
            if (global.location.hash !== _hash) {
                global.location.hash = _hash;
            }
        };

        global.onload = function () {
            noBackPlease();

            // Disable backspace key except on input fields and textarea
            document.body.onkeydown = function (e) {
                var elm = e.target.nodeName.toLowerCase();
                if (e.which === 8 && (elm !== 'input' && elm !== 'textarea')) {
                    e.preventDefault();
                }
                // Stop the event from propagating up the DOM tree
                e.stopPropagation();
            };
        };
    })(window);
</script>

</html>
