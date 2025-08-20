@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard - Analytics'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection


@section('page-script')
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debt Progress Chart
            const debtProgress = new ApexCharts(document.querySelector("#debt-progress"), {
                series: [{{ ($TotalPaidDebt / $TotalDebt) * 100 }}],
                chart: {
                    height: 300,
                    type: 'radialBar'
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '70%'
                        },
                        dataLabels: {
                            name: {
                                fontSize: '22px'
                            },
                            value: {
                                fontSize: '16px'
                            },
                            total: {
                                show: true,
                                label: '{{ __("Paid") }}',
                                formatter: function(w) {
                                    return '{{ number_format(($TotalPaidDebt / $TotalDebt) * 100, 1) }}%'
                                }
                            }
                        }
                    }
                },
                colors: ['#7367F0'],
                labels: ['{{ __("Paid Debt") }}']
            }).render();

            // Fuel Type Distribution
            const fuelTypeChart = new ApexCharts(document.querySelector("#fuel-type-chart"), {
                series: Object.values(@json($fuelTypes)),
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: Object.keys(@json($fuelTypes)),
                colors: ['#00CFE8', '#FF9F43', '#EA5455'],
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        }
                    }
                }]
            }).render();

            // Payment Status Chart
            const paymentStatusChart = new ApexCharts(document.querySelector("#payment-status-chart"), {
                series: Object.values(@json($paymentStatus)),
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: Object.keys(@json($paymentStatus)),
                colors: ['#28C76F', '#EA5455'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        }
                    }
                }]
            }).render();

            // Debt Timeline Chart
            const debtTimeline = new ApexCharts(document.querySelector("#debt-timeline"), {
                series: [{
                    name: '{{ __("Total Debt") }}',
                    data: @json($debtTimeline->map(fn($item) => $item->total))
                }],
                chart: {
                    type: 'line',
                    height: 350
                },
                xaxis: {
                    categories: @json($debtTimeline->map(fn($item) => ($item->year ?? '0000') . '-' . ($item->month ?? '00')))
                },
                colors: ['#7367F0'],
                stroke: {
                    width: 3
                },
                markers: {
                    size: 5
                }
            }).render();

            // Fuel Consumption Timeline
            const fuelTimeline = new ApexCharts(document.querySelector("#fuel-timeline"), {
                series: [{
                    name: '{{ __("Liters") }}',
                    data: @json($fuelTimeline->map(fn($item) => $item->liters))
                }, {
                    name: '{{ __("Amount") }}',
                    data: @json($fuelTimeline->map(fn($item) => $item->amount))
                }],
                chart: {
                    type: 'area',
                    height: 350
                },
                xaxis: {
                    categories: @json($fuelTimeline->map(fn($item) => $item->year . '-' . $item->month))
                },
                colors: ['#00CFE8', '#FF9F43'],
                stroke: {
                    curve: 'smooth'
                }
            }).render();
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <!-- Debt Statistics -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-2">{{ __('Total Debts') }}</span>
                                <h3 class="card-title mb-0">{{ number_format($TotalDebt, 2) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-primary rounded-circle">
                                <i class='bx bx-credit-card bx-lg'></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Paid') }}</span>
                                <span class="text-success">{{ number_format($TotalPaidDebt, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Outstanding') }}</span>
                                <span class="text-danger">{{ number_format($TotalRestDebt, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fuel Statistics -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-2">{{ __('Total Fuel') }}</span>
                                <h3 class="card-title mb-0">{{ number_format($TotalFuel, 2) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-warning rounded-circle">
                                <i class='bx bx-gas-pump bx-lg'></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Paid') }}</span>
                                <span class="text-success">{{ number_format($TotalPaidFuel, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Unpaid') }}</span>
                                <span class="text-danger">{{ number_format($TotalUnPaidFuel, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liters Summary -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-2">{{ __('Total Liters') }}</span>
                                <h3 class="card-title mb-0">{{ number_format($TotalLiter, 2) }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-label-success rounded-circle">
                                <i class='bx bx-water bx-lg'></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Diesel') }}</span>
                                <span class="text-info">{{ number_format($getTotalLiterTypeDiesl, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Gasoline') }}</span>
                                <span class="text-warning">{{ number_format($TotalLiterGasoline, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics Row -->
        <div class="row">
            <!-- Fuel Types Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ __('Fuel Type Breakdown') }}</h5>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar avatar-sm bg-label-info me-3">
                                        <i class='bx bx-oil'></i>
                                    </div>
                                    <div>
                                        <span class="text-muted">{{ __('Diesel') }}</span>
                                        <h5 class="mb-0">{{ number_format($TotalAmountTypeDiesel, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-label-warning me-3">
                                        <i class='bx bx-gas-pump'></i>
                                    </div>
                                    <div>
                                        <span class="text-muted">{{ __('Gasoline') }}</span>
                                        <h5 class="mb-0">{{ number_format($TotalAmountGasoline, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar avatar-sm bg-label-danger me-3">
                                        <i class='bx bx-wind'></i>
                                    </div>
                                    <div>
                                        <span class="text-muted">{{ __('Gas') }}</span>
                                        <h5 class="mb-0">{{ number_format($TotalLiterGas, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ __('Payment Overview') }}</h5>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-4" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($TotalPaidDebt / $TotalDebt) * 100 }}%"
                                aria-valuenow="{{ ($TotalPaidDebt / $TotalDebt) * 100 }}" aria-valuemin="0"
                                aria-valuemax="100">
                                {{ number_format(($TotalPaidDebt / $TotalDebt) * 100, 1) }}%
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ __('Paid Debt') }}</span>
                                    <span class="fw-semibold">{{ number_format($TotalPaidDebt, 2) }}</span>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ __('Outstanding Debt') }}</span>
                                    <span class="fw-semibold text-danger">{{ number_format($TotalRestDebt, 2) }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ __('Total Fuel Cost') }}</span>
                                    <span class="fw-semibold">{{ number_format($TotalFuel, 2) }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <!-- Debt Section -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Debt Progress') }}</h5>
                </div>
                <div class="card-body">
                    <div id="debt-progress"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Debt Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div id="debt-timeline"></div>
                </div>
            </div>
        </div>

        <!-- Fuel Section -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Fuel Type Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <div id="fuel-type-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Payment Status') }}</h5>
                </div>
                <div class="card-body">
                    <div id="payment-status-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Fuel Consumption Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div id="fuel-timeline"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
