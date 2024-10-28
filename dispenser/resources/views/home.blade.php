@extends('adminlte::page')

{{-- Extend and customize the browser title --}}
@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

{{-- Extend and customize the page content header --}}
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

{{-- Rename section content to content_body --}}
@section('content')
    @yield('content_body')
    
    {{-- Chart and Summary Container --}}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Overall container -->
                <div class="table-container p-3 border rounded position-relative">
                    <!-- Common container for chart and summary -->
                    <div class="row">
                        <div class="col-md-5">
                            <!-- Chart column -->
                            <div class="chart-container text-center">
                                <canvas id="voucherChart" width="200" height="200"></canvas>
                            </div>
                        </div>

                        <!-- Vertical divider -->
                        <div class="col-md-1 d-flex justify-content-center align-items-center">
                            <div class="vertical-divider"></div>
                        </div>

                        <div class="col-md-5">
                            <!-- Summary column -->
                            <div class="summary-box">
                                <h3>Summary</h3>
                                <p>Total Given Vouchers: <strong>{{ $data['values'][0] }}</strong></p>
                                <p>Total Not Given Vouchers: <strong>{{ $data['values'][1] }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Report Button positioned in the lower right -->
                    <div class="position-absolute generate-report-btn">
                        <div class="dropdown d-inline-block">
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
            </div>
        </div>
    </div>

    {{-- Chart.js and Chart Data --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        const ctx = document.getElementById('voucherChart').getContext('2d');
        
        // Accessing the data from the controller
        const labels = @json($data['labels']);
        const values = @json($data['values']);

        const voucherChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Voucher Status',
                    data: values,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        formatter: (value, context) => {
                            return value;
                        },
                        color: '#fff',
                    },
                }
            },
            plugins: [ChartDataLabels]
        });

        function generateReport(timeframe) {
            window.open(`/reports/vouchers?timeframe=${timeframe}`, '_blank');
        }
    </script>
@stop

{{-- Create a common footer --}}
@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>
    <strong>
        <a href="{{ config('app.company_url', '#') }}">
            {{ config('app.company_name', 'NDMU MIS') }}
        </a>
    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}
@push('js')
<script>
    $(document).ready(function() {
        // Add your common script logic here...
    });
</script>
@endpush

{{-- Add common CSS customizations --}}
@push('css')
<style type="text/css">
    /* Custom styles for the vertical divider */
    .vertical-divider {
        width: 2px;
        height: 100%;
        background-color: #ddd;
    }

    /* Position Generate Report button in the lower right */
    .generate-report-btn {
        bottom: 15px;
        right: 15px;
    }
</style>
@endpush
