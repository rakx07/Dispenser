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

        /* ===== Base ===== */
        html, body { height: 100%; }
        body{
            min-height:100vh;
            background: linear-gradient(180deg, rgba(11,61,46,0.08), rgba(227,199,122,0.10));
            color: var(--ink);
            display:flex;
            flex-direction:column;
        }

        /* ===== Navbar ===== */
        .ndmu-navbar{
            background: var(--ndmu-green);
            border-bottom: 4px solid var(--ndmu-gold);
            box-shadow: 0 10px 22px rgba(0,0,0,.10);
        }

        .brand-title{
            font-weight: 900;
            letter-spacing: .2px;
            line-height: 1.1;
        }

        .page-title{
            font-weight: 950;
            color: var(--ndmu-green);
            letter-spacing: .2px;
        }

        /* ===== Hero strip ===== */
        .hero-strip{
            background: linear-gradient(90deg, rgba(11,61,46,.08), rgba(227,199,122,.14));
            border: 2px solid rgba(11,61,46,0.18);
            border-radius: 18px;
            padding: 14px 16px;
            box-shadow: 0 12px 26px rgba(0,0,0,.06);
        }

        /* ===== Cards ===== */
        .card-ndmu{
            background:#fff;
            border-radius: 18px;
            border: 2px solid rgba(11,61,46,0.20);
            box-shadow: 0 14px 28px rgba(0,0,0,.06);
            overflow: hidden;
        }

        .card-ndmu .card-header{
            background:
                linear-gradient(90deg, rgba(11,61,46,0.10), rgba(227,199,122,0.14));
            border-bottom: 1px solid rgba(11,61,46,0.14);
            font-weight: 950;
            color: var(--ndmu-green);
            padding: 12px 16px;
        }

        .card-ndmu .card-body{
            padding: 16px;
        }

        .help-chip{
            display:inline-flex;
            gap:.5rem;
            align-items:center;
            padding:.35rem .7rem;
            border-radius: 999px;
            background: rgba(227,199,122,.18);
            border:1px solid rgba(227,199,122,.55);
            color: #fff;
            font-weight: 900;
        }

        /* ===== Buttons ===== */
        .btn-ndmu{
            background: var(--ndmu-green);
            border-color: var(--ndmu-green);
            color: #ffffff !important;
            font-weight: 900;
            border-radius: 14px;
            padding: .7rem 1rem;
            box-shadow: 0 10px 18px rgba(11,61,46,.18);
        }

        .btn-ndmu:hover{
            background:#082c21;
            border-color:#082c21;
            color:#ffffff !important;
        }

        .btn-outline-ndmu{
            border:2px solid rgba(11,61,46,0.30);
            color: var(--ndmu-green);
            font-weight: 900;
            background:#fff;
            border-radius: 14px;
            padding: .7rem 1rem;
        }
        .btn-outline-ndmu:hover{
            background: rgba(11,61,46,0.06);
            color: var(--ndmu-green);
        }

        /* ===== Note box ===== */
        .note-box{
            background: var(--paper);
            border: 2px dashed rgba(11,61,46,0.28);
            border-radius: 16px;
            padding: 12px 14px;
        }

        .mini-badge{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            padding:.25rem .55rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: .85rem;
            color: var(--ndmu-green);
            background: rgba(11,61,46,.08);
            border: 1px solid rgba(11,61,46,.18);
        }

        /* ===== Guidelines list (nicer) ===== */
        .guidelines{
            margin:0;
            padding:0;
            list-style:none;
            display:grid;
            gap:10px;
        }
        .guidelines li{
            display:flex;
            gap:10px;
            align-items:flex-start;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(11,61,46,.14);
            background: rgba(255,255,255,.75);
        }
        .guidelines .dot{
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-top: 6px;
            background: var(--ndmu-gold);
            box-shadow: 0 0 0 3px rgba(227,199,122,.25);
            flex: 0 0 10px;
        }
        .guidelines b{
            color: var(--ndmu-green);
        }

        /* ===== Footer ===== */
        footer{
            margin-top:auto;
            background: var(--ndmu-green);
            color: #fff;
            text-align:center;
            padding: .9rem;
            font-weight: 900;
            border-top: 4px solid var(--ndmu-gold);
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

        /* Input polish */
        .form-control{
            border-radius: 14px;
            padding: .65rem .85rem;
        }
        .form-control:focus{
            box-shadow: 0 0 0 .2rem rgba(11,61,46,.10);
            border-color: rgba(11,61,46,.35);
        }
        .bootstrap-select > .dropdown-toggle{
            border-radius: 14px !important;
            padding: .65rem .85rem;
        }

        /* Responsive spacing */
        @media (max-width: 576px){
            .hero-strip{ padding: 12px 12px; border-radius: 16px; }
            .card-ndmu{ border-radius: 16px; }
            .card-ndmu .card-body{ padding: 14px; }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar ndmu-navbar sticky-top">
        <div class="container-fluid px-3">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('ndmulogo.png') }}" alt="NDMU Logo" width="36" height="36" class="rounded" />
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
        <!-- Header -->
        <div class="text-center mb-3 mb-md-4">
            <h1 class="page-title mb-1">Student Credentials Dispenser</h1>
            <div class="text-muted">
                Enter your details to view your WiFi and system credentials.
            </div>
        </div>

        <!-- Hero strip -->
        <div class="hero-strip mb-3 mb-md-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
            <div>
                <div class="mini-badge mb-2">Quick Reminder</div>
                <div class="fw-bold" style="color:var(--ndmu-green);">
                    Use your correct <span style="color:#0a2f23;">Course</span>, <span style="color:#0a2f23;">Last Name</span>, and <span style="color:#0a2f23;">Birthday</span>.
                </div>
                <div class="text-muted small">
                    Your information must match the records.
                </div>
            </div>
            <div class="mini-badge">
                Private kiosk • Do not share your credentials
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
                            <div style="font-weight:950; color:var(--ndmu-green);">Accepted ID</div>
                            <div class="text-muted">
                                You may enter either:
                                <b>EIS ID (School ID)</b> or <b>Kumosoft ID</b>.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('students.voucherAndSatp') }}" id="studentForm">
                            @csrf

                            <div class="mb-3">
                                <label for="courseSelect" class="form-label" style="font-weight:950;">Select Your Course</label>
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
                                <label for="idNumber" class="form-label" style="font-weight:950;">
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

                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label for="lastname" class="form-label" style="font-weight:950;">Last Name</label>
                                    <input type="text"
                                           name="lastname"
                                           placeholder="Enter your last name"
                                           class="form-control"
                                           id="lastname"
                                           required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="birthday" class="form-label" style="font-weight:950;">
                                        Birthday <small class="text-muted">(yyyy-mm-dd)</small>
                                    </label>
                                    <input type="text"
                                           name="birthday"
                                           placeholder="e.g. 2001-01-29"
                                           class="form-control"
                                           id="birthday"
                                           required>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
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
                        <ul class="guidelines">
                            <li><span class="dot"></span><div><b>For enrolled NDMU students only.</b> Voucher access is exclusive.</div></li>
                            <li><span class="dot"></span><div><b>One voucher per student.</b> Please use responsibly.</div></li>
                            <li><span class="dot"></span><div><b>Works on NDMUWLAN1 / NDMUWDS</b> and similar access points.</div></li>
                            <li><span class="dot"></span><div><b>2 hours access.</b> Auto disconnect after time expires. Use same voucher code for reconnection.</div></li>
                            <li><span class="dot"></span><div><b>Need help?</b> Proceed to ICT/MIS Office.</div></li>
                        </ul>

                        <div class="note-box mt-3">
                            <div style="font-weight:950; color:var(--ndmu-green);">Tip</div>
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