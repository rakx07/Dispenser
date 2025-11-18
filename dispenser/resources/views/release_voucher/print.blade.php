{{-- resources/views/release_voucher/print.blade.php --}}
@extends('adminlte::page')

@section('title', 'Print Vouchers')

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <h3>Print Voucher(s)</h3>
                <hr>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container" id="printArea">
        @if (!isset($vouchers) || $vouchers->count() === 0)
            <div class="alert alert-warning">
                No vouchers found to print.
                <br>
                <a href="{{ route('release-voucher.index') }}" class="btn btn-secondary btn-sm mt-2">
                    Back to Release Page
                </a>
            </div>
        @else
            {{-- HEADER --}}
            <div class="text-center mb-3">
                {{-- Optional logo --}}
                {{-- <img src="{{ asset('img/ndmu-logo.png') }}" alt="Logo" height="60"> --}}
                <h4 class="mb-0">Voucher Release Slip</h4>
                <small>NDMU Dispenser System</small>
            </div>

            <div class="d-flex justify-content-between mb-2" style="font-size: 12px;">
                <span><strong>Date/Time:</strong> {{ now()->format('Y-m-d H:i') }}</span>
                {{-- Add recipient later if you pass it from controller --}}
                {{-- <span><strong>Recipient:</strong> {{ $student->school_id }} - {{ $student->name }}</span> --}}
            </div>

            <hr class="mb-3">

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:70px;">ID</th>
                            <th>Voucher Code</th>
                            <th style="width:90px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vouchers as $i => $voucher)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $voucher->id }}</td>
                                <td>{{ $voucher->voucher_code ?? 'N/A' }}</td>
                                <td>{{ $voucher->is_given ? 'Given' : 'Available' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- SIGNATURES --}}
            <div class="row mt-4" style="font-size: 12px;">
                <div class="col-md-6 text-center">
                    <p class="mb-5">______________________________</p>
                    <p class="mb-0"><strong>Recipient Signature</strong></p>
                    <p class="mb-0 text-muted" style="font-size: 11px;">Date &amp; Time Received</p>
                </div>
                <div class="col-md-6 text-center">
                    <p class="mb-5">______________________________</p>
                    <p class="mb-0"><strong>Releasing Officer</strong></p>
                    <p class="mb-0 text-muted" style="font-size: 11px;">Name &amp; Signature</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        @media print {
            /* Hide AdminLTE chrome, buttons, etc. */
            body.sidebar-mini .main-sidebar,
            body.sidebar-mini .main-header,
            .content-header {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            #printArea {
                margin: 0;
                padding: 10mm 15mm;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto-open print dialog when page loads
        window.addEventListener('load', function () {
            if (window.print) {
                window.print();
            }
        });
    </script>
@endpush
