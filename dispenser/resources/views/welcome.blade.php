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
            WiFi Access Code Manager
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-5">
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

                <form method="POST" action="{{ route('voucher.show') }}">
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
                    <li class="list-group-item"><strong>• This voucher code is exclusively for summer-enrolled students of Notre Dame of Marbel University.</strong></li>
                    <li class="list-group-item"><strong>• Each student is entitled to use one voucher code.</strong></li>
                    <li class="list-group-item"><strong>• The voucher code is valid for use with NDMUWLAN1, NDMUWDS, and similar WiFi access points.</strong></li>
                    <li class="list-group-item"><strong>• The voucher code provides 2 hours of access; the connection will be automatically disconnected after this time, requiring you to reconnect using the same voucher code.</strong></li>
                    <!-- <li class="list-group-item"><strong>• Trial Run (Only students enrolled during summer)</strong></li> -->
                </ul>
            </div>
        </div>
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
                document.getElementById('courseSelect').selectedIndex = 0;
                $('.selectpicker').selectpicker('refresh');
                document.getElementById('idNumber').value = '';
                document.getElementById('lastname').value = '';
                document.getElementById('birthday').value = '';
            });
        });
    </script>
    <script type="text/javascript">
        (function (global) {
            if(typeof (global) === "undefined") {
                throw new Error("window is undefined");
            }

            var _hash = "!";
            var noBackPlease = function () {
                global.location.href += "#";

                // Making sure we have the fruit available for juice (^__^)
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

                // Disables backspace on page except on input fields and textarea..
                document.body.onkeydown = function (e) {
                    var elm = e.target.nodeName.toLowerCase();
                    if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                        e.preventDefault();
                    }
                    // Stopping the event bubbling up the DOM tree...
                    e.stopPropagation();
                };
            };
        })(window);
    </script>

</body>
</html>
