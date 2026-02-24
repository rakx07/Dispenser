<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDMU Credentials Dispenser</title>

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Local Bootstrap Select CSS -->
    <link href="{{ asset('assets/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <!-- Local SweetAlert2 JS -->
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

    <style>
        :root{
            --ndmu-green:#0B3D2E;
            --ndmu-gold:#E3C77A;
            --paper:#FFFBF0;
            --ink:#1f2937;
        }

        body{
            min-height:100vh;
            background: linear-gradient(180deg, rgba(11,61,46,0.08), rgba(227,199,122,0.10));
            color: var(--ink);
            display:flex;
            flex-direction:column;
        }

        .ndmu-navbar{
            background: var(--ndmu-green);
            border-bottom: 3px solid var(--ndmu-gold);
        }

        .brand-title{
            font-weight: 900;
            letter-spacing: .2px;
        }

        .page-title{
            font-weight: 900;
            color: var(--ndmu-green);
        }

        .card-ndmu{
            background:#fff;
            border-radius: 16px;
            border: 2px solid rgba(11,61,46,0.22);
            box-shadow: 0 10px 24px rgba(0,0,0,.06);
        }

        .card-ndmu .card-header{
            background: linear-gradient(90deg, rgba(11,61,46,0.10), rgba(227,199,122,0.12));
            border-bottom: 1px solid rgba(11,61,46,0.15);
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            font-weight: 900;
            color: var(--ndmu-green);
        }

        .help-chip{
            display:inline-flex;
            gap:.5rem;
            align-items:center;
            padding:.35rem .6rem;
            border-radius: 999px;
            background: rgba(227,199,122,.18);
            border:1px solid rgba(227,199,122,.55);
            color: #fff;
            font-weight: 800;
        }

        .btn-ndmu{
            background: var(--ndmu-green);
            border-color: var(--ndmu-green);
            color: #ffffff !important;
            font-weight: 900;
        }

        .btn-ndmu:hover{
            background:#082c21;
            border-color:#082c21;
            color:#ffffff !important;
        }

        .btn-outline-ndmu{
            border:2px solid rgba(11,61,46,0.35);
            color: var(--ndmu-green);
            font-weight: 900;
            background:#fff;
        }
        .btn-outline-ndmu:hover{
            background: rgba(11,61,46,0.06);
            color: var(--ndmu-green);
        }

        .note-box{
            background: var(--paper);
            border: 2px dashed rgba(11,61,46,0.28);
            border-radius: 14px;
            padding: 12px 14px;
        }

        footer{
            margin-top:auto;
            background: var(--ndmu-green);
            color: #fff;
            text-align:center;
            padding: .9rem;
            font-weight: 800;
            border-top: 3px solid var(--ndmu-gold);
        }

        /* Bootstrap-select tweaks */
        .bootstrap-select .dropdown-menu{
            max-height: 320px !important;
            overflow-y: auto !important;
            white-space: normal !important;
        }
        .bootstrap-select .dropdown-menu .inner,
        .bootstrap-select .dropdown-menu li{
            white-space: normal !important;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar ndmu-navbar sticky-top">
        <div class="container-fluid px-3">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('ndmulogo.png') }}" alt="NDMU Logo" width="34" height="34" class="rounded" />
                <div class="text-white">
                    <div class="brand-title">Notre Dame of Marbel University</div>
                    <div class="small text-white-50">WiFi Access Code Manager</div>
                </div>
            </div>

            <span class="help-chip">
                <span>Self-Service Kiosk</span>
            </span>
        </div>
    </nav>

    <main class="container py-4 py-md-5">
        <div class="text-center mb-4">
            <h1 class="page-title mb-1">Student Credentials Dispenser</h1>
            <div class="text-muted">
                Enter your details to view your WiFi and system credentials.
            </div>
        </div>

        <div class="row g-3 g-md-4">
            <!-- LEFT: FORM -->
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">
                        Student Information
                    </div>
                    <div class="card-body">

                        {{-- Alerts --}}
                        @if (session('message'))
                            <div class="alert alert-info mb-3">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <b>Please fix the following:</b>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="note-box mb-3">
                            <div style="font-weight:900; color:var(--ndmu-green);">Accepted ID</div>
                            <div class="text-muted">
                                You may enter either:
                                <b>EIS ID (School ID)</b> or <b>Kumosoft ID</b>.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('students.voucherAndSatp') }}" id="studentForm">
                            @csrf

                            <div class="mb-3">
                                <label for="courseSelect" class="form-label" style="font-weight:900;">Select Your Course</label>
                                <div class="w-100 mt-2">
                                    <select class="form-control selectpicker"
                                            id="courseSelect"
                                            name="courseSelect"
                                            data-live-search="true"
                                            title="Select your course..."
                                            required>
                                        @foreach ($courses as $course)
                                            <option data-subtext="{{ $course->code }}" value="{{ $course->code }}">
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="idNumber" class="form-label" style="font-weight:900;">
                                    EIS ID (School ID) / Kumosoft ID
                                </label>
                                <input type="text"
                                       name="idNumber"
                                       placeholder="Enter EIS ID or Kumosoft ID"
                                       class="form-control"
                                       id="idNumber"
                                       required>
                                <small class="text-muted">
                                    Example: 202412345 (EIS) or your Kumosoft ID.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="lastname" class="form-label" style="font-weight:900;">Last Name</label>
                                <input type="text"
                                       name="lastname"
                                       placeholder="Enter your last name"
                                       class="form-control"
                                       id="lastname"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="birthday" class="form-label" style="font-weight:900;">
                                    Birthday <small class="text-muted">(yyyy-mm-dd)</small>
                                </label>
                                <input type="text"
                                       name="birthday"
                                       placeholder="e.g. 2001-01-29"
                                       class="form-control"
                                       id="birthday"
                                       required>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="submit" class="btn btn-ndmu w-100">
                                    Submit
                                </button>
                                <button type="button" class="btn btn-outline-ndmu w-100" id="clearButton">
                                    Clear
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- RIGHT: GUIDELINES -->
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">
                        Voucher Usage Guidelines
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>• This voucher code is exclusively for enrolled students of Notre Dame of Marbel University.</strong></li>
                            <li class="list-group-item"><strong>• Each student is entitled to use one voucher code.</strong></li>
                            <li class="list-group-item"><strong>• The voucher code is valid for use with NDMUWLAN1, NDMUWDS, and similar WiFi access points.</strong></li>
                            <li class="list-group-item"><strong>• The voucher code provides 2 hours of access; the connection will be automatically disconnected after this time.</strong></li>
                            <li class="list-group-item"><strong>• If there are concerns kindly proceed to MIS Office.</strong></li>
                        </ul>

                        <div class="note-box mt-3">
                            <div style="font-weight:900; color:var(--ndmu-green);">Tip</div>
                            <div class="text-muted">
                                Double-check your <b>course</b>, <b>last name</b>, and <b>birthday</b> before submitting.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        NDMU © 2026 | Developed by ICT Department
    </footer>

    <!-- Local Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Local jQuery -->
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <!-- Local Bootstrap Select JS -->
    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Init selectpicker
            $('.selectpicker').selectpicker();

            // Clear button
            document.getElementById('clearButton').addEventListener('click', function() {
                document.getElementById('studentForm').reset();
                $('#courseSelect').selectpicker('val', '');
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

                document.body.onkeydown = function (e) {
                    var elm = e.target.nodeName.toLowerCase();
                    if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                        e.preventDefault();
                    }
                    e.stopPropagation();
                };
            };
        })(window);
    </script>
</body>
</html>