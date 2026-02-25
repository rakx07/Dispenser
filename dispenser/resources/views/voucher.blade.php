<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDMU Credentials Dispenser</title>

    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <style>
        :root{
            --ndmu-green:#0B3D2E;
            --ndmu-gold:#E3C77A;
            --paper:#FFFBF0;
            --ink:#1f2937;
        }

        html, body { height: 100%; }
        body{
            margin:0;
            background: linear-gradient(180deg, rgba(11,61,46,0.08), rgba(227,199,122,0.10));
            display:flex;
            flex-direction:column;
            color:var(--ink);
            overflow:hidden;
            /* Bigger than before but still responsive */
            font-size: clamp(14px, 1.25vw, 16px);
        }

        .ndmu-navbar{
            background:var(--ndmu-green);
            border-bottom:3px solid var(--ndmu-gold);
            padding-top:.45rem;
            padding-bottom:.45rem;
        }

        .brand-title{ font-weight:900; line-height:1.1; font-size: 1.02em; }
        .subtitle{ font-size:.92em; opacity:.9; }

        main{ flex:1; overflow:hidden; }

        .viewport-wrap{
            height: calc(100vh - 58px - 44px); /* slightly bigger navbar/footer allowance */
            padding: .9rem .9rem .75rem;
            overflow:hidden;
        }

        .page-title{
            font-weight:900;
            color:var(--ndmu-green);
            margin:0;
            font-size: clamp(18px, 2.05vw, 26px);
        }
        .page-sub{
            margin:.25rem 0 0;
            color:#6b7280;
            font-size: 1em;
        }

        .hero{
            background:#fff;
            border-radius:14px;
            border:1px solid rgba(11,61,46,.22);
            padding: .85rem 1rem;
            box-shadow:0 8px 18px rgba(0,0,0,.05);
        }

        .cards-grid{
            display:grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        @media (max-width: 992px){
            /* allow natural scroll on small screens */
            body{ overflow:auto; }
            main{ overflow:auto; }
            .viewport-wrap{ height:auto; overflow:visible; }
            .cards-grid{ grid-template-columns: 1fr; }
        }

        .card-ndmu{
            background:#fff;
            border-radius:14px;
            border:1px solid rgba(11,61,46,.22);
            box-shadow:0 8px 18px rgba(0,0,0,.04);
            height:100%;
            display:flex;
            flex-direction:column;
            min-height: 128px; /* slightly bigger baseline */
        }

        .card-ndmu .card-header{
            padding: .55rem .85rem;
            background:linear-gradient(90deg, rgba(11,61,46,.09), rgba(227,199,122,.12));
            font-weight:900;
            color:var(--ndmu-green);
            border-bottom:1px solid rgba(11,61,46,.12);
            border-top-left-radius:14px;
            border-top-right-radius:14px;
            font-size: 1.05em;
        }

        .card-ndmu .card-body{
            padding: .7rem .85rem .8rem;
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            gap:.45rem;
        }

        .value-mask{
            letter-spacing:2px;
            font-weight:900;
        }

        .small-link{
            word-break: break-word;
            font-size: 1em;
            color:#4b5563;
        }

        .subnote{
            font-size: .95em;
            color:#6b7280;
            line-height:1.25;
        }

        .actions{
            position: sticky;
            bottom: 0;
            padding-top: .7rem;
            margin-top: .65rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.0), rgba(255,255,255,0.85) 30%, rgba(255,255,255,1));
            border-top:1px dashed rgba(11,61,46,.25);
        }

        .btn-ndmu{
            background:var(--ndmu-green);
            border:none;
            font-weight:900;
            border-radius:12px;
            padding:.75rem 1rem;
            color:#fff;
            font-size: 1.05em;
        }
        .btn-ndmu:hover{ background:#082c21; color:#fff; }

        .btn-gold{
            background:var(--ndmu-gold);
            border:none;
            font-weight:900;
            border-radius:12px;
            padding:.75rem 1rem;
            color:var(--ndmu-green);
            font-size: 1.05em;
        }
        .btn-gold:hover{ filter:brightness(.95); color:var(--ndmu-green); }

        footer{
            height: 44px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:var(--ndmu-green);
            color:#fff;
            font-weight:900;
            border-top:3px solid var(--ndmu-gold);
            font-size: 1em;
            padding:0 .75rem;
        }

        /* Extra-tight screens (still bigger than old) */
        @media (max-height: 720px){
            .viewport-wrap{ padding:.75rem .75rem .6rem; }
            .hero{ padding:.75rem .85rem; }
            .card-ndmu{ min-height: 120px; }
            .card-ndmu .card-body{ padding:.65rem .8rem .7rem; }
        }
    </style>
</head>

<body>
<nav class="navbar ndmu-navbar">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center gap-2 text-white">
            <img src="{{ asset('ndmulogo.png') }}" width="36" height="36" class="rounded" alt="NDMU">
            <div>
                <div class="brand-title">Notre Dame of Marbel University</div>
                <div class="subtitle">Student WiFi &amp; System Credentials</div>
            </div>
        </div>

        <span class="badge rounded-pill"
              style="background:rgba(227,199,122,.18); border:1px solid rgba(227,199,122,.55); color:#fff; font-weight:900; font-size:.95em;">
            Kiosk
        </span>
    </div>
</nav>

<main>
    <div class="viewport-wrap">
        <div class="text-center mb-2">
            <h1 class="page-title">Credentials Summary</h1>
            <p class="page-sub">Passwords are hidden. Tap <b>Show All</b> when ready.</p>
        </div>

        <div class="hero mb-2">
            @if(isset($student))
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                    <div>
                        <div class="fw-bold" style="font-size:1.12em;">
                            {{ ucfirst($student->firstname) }} {{ ucfirst($student->lastname) }}
                        </div>
                        <div class="text-muted" style="font-size:1em;">
                            <b>Course:</b> {{ optional($student->course)->name ?? 'Not Available' }}
                            &nbsp;•&nbsp;
                            <b>EIS ID:</b> {{ $student->school_id ?? 'Not Available' }}
                        </div>

                        @if(isset($displaySettings['voucher']) && $displaySettings['voucher']->is_enabled)
                            <div class="mt-2" style="font-size:1em;">
                                <span class="badge"
                                      style="background:rgba(11,61,46,.10); border:1px solid rgba(11,61,46,.25); color:var(--ndmu-green); font-weight:900; font-size:.95em;">
                                    WiFi Voucher
                                </span>
                                <span id="voucher-code-field" class="ms-2 value-mask">********</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-muted" style="font-size:.98em;">
                        <b>Tip:</b> Keep this screen private.
                    </div>
                </div>
            @else
                <div class="alert alert-danger mb-0">Student information not found.</div>
            @endif
        </div>

        <div class="cards-grid">

            @if(isset($displaySettings['email']) && $displaySettings['email']->is_enabled)
            <div class="card-ndmu">
                <div class="card-header">Email</div>
                <div class="card-body">
                    <div>
                        <div><b>Email:</b> {{ $email ?? 'Not Available' }}</div>
                        <div class="mt-1"><b>Password:</b> <span id="password-field" class="value-mask">********</span></div>
                    </div>
                    <div class="small-link">Google Workspace / Gmail</div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['kumosoft']) && $displaySettings['kumosoft']->is_enabled)
            <div class="card-ndmu">
                <div class="card-header">Kumosoft</div>
                <div class="card-body">
                    <div>
                        <div><b>Kumosoft ID / Kumosoft Email:</b></div>
                        <div class="text-muted" style="font-size:1em;">
                            {{ $kumosoft_id ?? 'Not Available' }}
                            @if(!empty($kumosoft_email))
                                &nbsp;•&nbsp; {{ $kumosoft_email }}
                            @endif
                        </div>
                        <div class="subnote mt-1">
                            You may use your <b>Kumosoft ID</b> or <b>registered Kumosoft email</b> as your username.
                        </div>
                        <div class="mt-2"><b>Password:</b> <span id="kumosoft-password" class="value-mask">********</span></div>
                    </div>
                    <div class="small-link"><b>Website:</b> sms.ndmu.edu.ph</div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['schoology']) && $displaySettings['schoology']->is_enabled)
            <div class="card-ndmu">
                <div class="card-header">Schoology</div>
                <div class="card-body">
                    <div>
                        <div><b>Username:</b> {{ $student->school_id ?? 'Not Available' }}</div>
                        <div class="mt-1"><b>Password:</b> <span id="password-field-schoology" class="value-mask">********</span></div>
                    </div>
                    <div class="small-link"><b>Website:</b> ndmu.schoology.com</div>
                </div>
            </div>
            @endif

            @if(isset($displaySettings['satp']) && $displaySettings['satp']->is_enabled)
            <div class="card-ndmu">
                <div class="card-header">SATP</div>
                <div class="card-body">
                    <div>
                        <div><b>Username:</b> {{ $student->school_id ?? 'Not Available' }}</div>
                        <div class="mt-1"><b>Password:</b> <span id="satp-password-field" class="value-mask">********</span></div>
                    </div>
                    <div class="small-link">
                        <span class="text-danger fw-bold">Use Google Chrome:</span>
                        <b>Website:</b> satp.ndmu.edu.ph
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="actions">
            <div class="d-grid gap-2" style="max-width:520px; margin:0 auto;">
                <button id="toggle-all" class="btn btn-gold">
                    🔓 Show All
                </button>
                <button id="doneButton" class="btn btn-ndmu">
                    ✔ Done
                </button>
            </div>
        </div>

    </div>
</main>

<footer>
    NDMU © 2024 | Developed by MIS Department
</footer>

<script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let transactionRecorded = false;

    const toggleAllBtn = document.getElementById('toggle-all');
    const doneBtn = document.getElementById('doneButton');

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
        .then(r => r.json())
        .then(data => console.log("Transaction:", data))
        .catch(err => console.error("Error:", err));
    }

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

    if (doneBtn) {
        doneBtn.addEventListener('click', function () {
            Swal.fire({
                icon: 'success',
                title: 'Thank you!',
                text: 'Have a nice day!',
                showConfirmButton: false,
                timer: 1400
            }).then(() => {
                window.location.href = "{{ route('welcome') }}";
            });
        });
    }

    // Auto return after inactivity (40s)
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