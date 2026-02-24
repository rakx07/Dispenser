<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDMU Credentials Dispenser</title>

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

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
            font-weight: 800;
            letter-spacing: .2px;
        }

        .page-title{
            font-weight: 900;
            color: var(--ndmu-green);
        }

        .hero{
            background: var(--paper);
            border: 2px solid rgba(11,61,46,0.25);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,.06);
        }

        .card-ndmu{
            background: #ffffff;
            border-radius: 16px;
            border: 2px solid rgba(11,61,46,0.22);
            box-shadow: 0 10px 24px rgba(0,0,0,.06);
            height: 100%;
        }

        .card-ndmu .card-header{
            background: linear-gradient(90deg, rgba(11,61,46,0.10), rgba(227,199,122,0.12));
            border-bottom: 1px solid rgba(11,61,46,0.15);
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            font-weight: 900;
            color: var(--ndmu-green);
        }

        .value-mask{
            letter-spacing: 2px;
        }

        .muted{
            color:#6b7280;
        }

        .btn-ndmu{
            background: var(--ndmu-green);
            border-color: var(--ndmu-green);
            color: #ffffff !important;
            font-weight: 800;
        }

        .btn-ndmu:hover{
            background: #082c21;
            border-color: #082c21;
            color: #ffffff !important;
        }

        .btn-gold{
            background: var(--ndmu-gold);
            border-color: var(--ndmu-gold);
            color: var(--ndmu-green);
            font-weight: 900;
        }
        .btn-gold:hover{
            filter: brightness(.95);
            color: var(--ndmu-green);
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

        .small-link{
            word-break: break-word;
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
                    <div class="small text-white-50">Student WiFi & System Credentials</div>
                </div>
            </div>

            <span class="badge rounded-pill" style="background:rgba(227,199,122,.18); border:1px solid rgba(227,199,122,.55); color:#fff; font-weight:800;">
                Kiosk Dispenser
            </span>
        </div>
    </nav>

    <main class="container py-4 py-md-5">
        <div class="text-center mb-4">
            <h1 class="page-title mb-1">Credentials Summary</h1>
            <div class="muted">Please click <b>Show All</b> only when you are ready to view your passwords.</div>
        </div>

        <!-- Student Header -->
        <div class="hero mb-4">
            @if(isset($student))
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="h5 mb-1" style="font-weight:900;">
                            {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}
                        </div>
                        <div class="muted">
                            <b>Course:</b> {{ optional($student->course)->name ?? 'Not Available' }}
                            &nbsp;•&nbsp;
                            <b>EIS ID:</b> {{ $student->school_id ?? 'Not Available' }}
                        </div>

                        @if(isset($displaySettings['voucher']) && $displaySettings['voucher']->is_enabled)
                            <div class="mt-2">
                                <span class="badge" style="background:rgba(11,61,46,.10); border:1px solid rgba(11,61,46,.25); color:var(--ndmu-green); font-weight:900;">
                                    WiFi Voucher:
                                </span>
                                <span id="voucher-code-field" class="ms-1 value-mask">********</span>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto">
                        <button id="toggle-all" class="btn btn-gold w-100 w-md-auto">
                            Show All
                        </button>
                        <button id="doneButton" class="btn btn-ndmu w-100 w-md-auto">
                            Done
                        </button>
                    </div>
                </div>
            @else
                <div class="alert alert-danger mb-0">Student information not found.</div>
            @endif
        </div>

        <!-- Credential Cards -->
        <div class="row g-3 g-md-4">
            @if(isset($displaySettings['email']) && $displaySettings['email']->is_enabled)
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">Email Credentials</div>
                    <div class="card-body">
                        <div class="mb-2"><b>Email:</b> {{ $email ?? 'Not Available' }}</div>
                        <div><b>Password:</b> <span id="password-field" class="value-mask">********</span></div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['kumosoft']) && $displaySettings['kumosoft']->is_enabled)
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">Kumosoft Credentials</div>
                    <div class="card-body">
                        <div class="mb-2"><b>EIS ID:</b> {{ $kumosoft_eis_id ?? ($student->school_id ?? 'Not Available') }}</div>
                        <div class="mb-2"><b>Kumosoft ID:</b> {{ $kumosoft_id ?? 'Not Available' }}</div>
                        <div class="mb-2"><b>Kumosoft Email:</b> {{ $kumosoft_email ?? 'Not Available' }}</div>
                        <div class="mb-2"><b>Kumosoft Password:</b> <span id="kumosoft-password" class="value-mask">********</span></div>
                        <div class="small-link"><b>Kumosoft Link:</b> https://sms.ndmu.edu.ph</div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['schoology']) && $displaySettings['schoology']->is_enabled)
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">Schoology Access</div>
                    <div class="card-body">
                        <div class="mb-2"><b>Username:</b> {{ $student->school_id ?? 'Not Available' }}</div>
                        <div class="mb-2"><b>Password:</b> <span id="password-field-schoology" class="value-mask">********</span></div>
                        <div class="small-link"><b>Schoology Link:</b> https://ndmu.schoology.com/</div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['satp']) && $displaySettings['satp']->is_enabled)
            <div class="col-12 col-lg-6">
                <div class="card card-ndmu">
                    <div class="card-header">SATP Credentials</div>
                    <div class="card-body">
                        <div class="mb-2"><b>SATP Username:</b> {{ $student->school_id ?? 'Not Available' }}</div>
                        <div class="mb-2"><b>SATP Password:</b> <span id="satp-password-field" class="value-mask">********</span></div>
                        <div class="small-link"><b>SATP Link:</b> http://satp.ndmu.edu.ph</div>
                        <div class="mt-2 text-danger" style="font-weight:900;">NOTE: Use Google Chrome Web Browser.</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>

    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <!-- Local JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let transactionRecorded = false;

        const toggleAllBtn = document.getElementById('toggle-all');
        const doneBtn = document.getElementById('doneButton');

        if (toggleAllBtn) {
            toggleAllBtn.addEventListener('click', function () {
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
        }

        function revealCredentials() {
            const passwordField = document.getElementById('password-field');
            if (passwordField) passwordField.textContent = @json($password ?? 'Not Available');

            const voucherField = document.getElementById('voucher-code-field');
            if (voucherField) voucherField.textContent = @json(optional($voucher)->voucher_code ?? 'Not Available');

            const satpField = document.getElementById('satp-password-field');
            if (satpField) satpField.textContent = @json($satp_password ?? 'Not Available');

            const schoologyField = document.getElementById('password-field-schoology');
            if (schoologyField) schoologyField.textContent = @json($schoology_credentials ?? 'Not Available');

            const kumosoftField = document.getElementById('kumosoft-password');
            if (kumosoftField) kumosoftField.textContent = @json($kumosoft_password ?? 'Not Available');
        }

        function recordTransaction() {
            fetch("{{ route('transactions.recordShowPassword') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    student_id: "{{ $student->id ?? '' }}"
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Transaction:", data);
            })
            .catch(error => console.error("Error:", error));
        }

        if (doneBtn) {
            doneBtn.addEventListener('click', function () {
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
        }

        // Auto return after inactivity
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