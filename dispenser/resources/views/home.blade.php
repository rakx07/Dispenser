@extends('adminlte::page')

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')
            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Voucher Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="voucherChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Student Voucher Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="studentVoucherChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Differences Section -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center" id="differencesDisplay">
                    <!-- This will display the calculated differences -->
                    <h5>Differences:</h5>
                    <p id="difference1">Calculating...</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="reportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Generate Report
                </button>
                <div class="dropdown-menu" aria-labelledby="reportDropdown">
                    <a class="dropdown-item" href="#" onclick="generateReport('day')">Today</a>
                    <a class="dropdown-item" href="#" onclick="generateReport('week')">This Week</a>
                    <a class="dropdown-item" href="#" onclick="generateReport('month')">This Month</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        // Data from the controller
        const voucherLabels = @json($voucherData['labels']);
        const voucherValues = @json($voucherData['values']);
        const studentVoucherLabels = @json($studentVoucherData['labels']);
        const studentVoucherValues = @json($studentVoucherData['values']);

        // Difference Calculation
        const studentsWithoutVoucher = studentVoucherValues[1]; // Second value in Student Distribution
        const notGivenVouchers = voucherValues[1]; // Second value in Voucher Status
        const difference1 = studentsWithoutVoucher - notGivenVouchers; // Students Without Voucher - Not Given Vouchers

        // Display the differences
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('difference1').textContent = `Difference 1: ${difference1} (Students Without Voucher - Not Given Vouchers)`;
        });

        // Chart Options
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                datalabels: {
                    formatter: value => value,
                    color: '#fff',
                },
            },
        };

        // Voucher Chart
        new Chart(document.getElementById('voucherChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: voucherLabels,
                datasets: [{
                    data: voucherValues,
                    backgroundColor: ['#4bc0c0', '#ff6384'],
                }]
            },
            options: chartOptions,
            plugins: [ChartDataLabels]
        });

        // Student Voucher Chart
        new Chart(document.getElementById('studentVoucherChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: studentVoucherLabels,
                datasets: [{
                    data: studentVoucherValues,
                    backgroundColor: ['#36a2eb', '#ffce56'],
                }]
            },
            options: chartOptions,
            plugins: [ChartDataLabels]
        });

        function generateReport(timeframe) {
            window.open(`/reports/vouchers?timeframe=${timeframe}`, '_blank');
        }
    </script>
@stop

@push('css')
<style>
    .card {
        height: 100%;
    }
    .card-body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 300px;
    }
    #differencesDisplay {
        margin-top: 20px;
        font-size: 1.2rem;
    }
</style>
@endpush
