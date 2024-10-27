
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
{{-- ADD THE CHART HERE --}}

<div class="container">
    <div class="row">
        <div class="col-md-12"> <!-- Overall container -->
            <div class="table-container p-3 border rounded"> <!-- Common container for chart and summary -->
                <div class="row">
                    <div class="col-md-6"> <!-- Chart column -->
                        <div class="chart-container text-center"> <!-- Enclosed chart in a div -->
                            <canvas id="voucherChart" width="200" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6"> <!-- Summary column -->
                        <div class="summary-box"> <!-- Enclosed summary in a div -->
                            <h3>Summary</h3>
                            <p>Total Given Vouchers: <strong>{{ $data['values'][0] }}</strong></p>
                            <p>Total Not Given Vouchers: <strong>{{ $data['values'][1] }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- Include data labels plugin -->
<script>
    const ctx = document.getElementById('voucherChart').getContext('2d');
    
    // Accessing the data from the controller
    const labels = @json($data['labels']);
    const values = @json($data['values']);

    const voucherChart = new Chart(ctx, {
        type: 'pie', // Set the chart type to 'pie'
        data: {
            labels: labels,
            datasets: [{
                label: 'Voucher Status',
                data: values,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',  // Color for 'Given'
                    'rgba(255, 99, 132, 0.6)'   // Color for 'Not Given'
                ],
                borderColor: 'rgba(255, 255, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Ensure the aspect ratio does not distort the size
            plugins: {
                legend: {
                    position: 'top',
                },
                datalabels: {
                    formatter: (value, context) => {
                        return value; // Display the numerical value
                    },
                    color: '#fff', // Color of the labels
                },
            }
        },
        plugins: [ChartDataLabels] // Enable the data labels plugin
    });
</script>

{{-- ADD THE CHART HERE --}}
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

    {{-- You can add AdminLTE customizations here --}}
    /*
    .card-header {
        border-bottom: none;
    }
    .card-title {
        font-weight: 600;
    }
    */

</style>
@endpush
