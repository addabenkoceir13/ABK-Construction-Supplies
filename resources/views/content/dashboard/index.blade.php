@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard - Analytics'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    .stat-card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }
    .progress-bar-animated {
        animation: progress-animation 1.5s ease-in-out;
    }
    @keyframes progress-animation {
        0% { width: 0; }
        100% { width: actual; }
    }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      // Debt Progress Chart
      const debtProgress = new ApexCharts(document.querySelector("#debt-progress"), {
          series: [{{ $TotalPaidDebt }}, {{ $TotalRestDebt }}],
          chart: { type: 'donut', height: 350 },
          labels: ['Paid Debt', 'Outstanding Debt'],
          colors: ['#00E396', '#FF4560'],
          legend: { position: 'bottom' },
          responsive: [{
              breakpoint: 480,
              options: { chart: { width: 200 } }
          }]
      }).render();

      // Fuel Type Distribution
      const fuelChart = new ApexCharts(document.querySelector("#fuel-chart"), {
          series: [{{ $TotalLiterGasoline }}, {{ $getTotalLiterTypeDiesl }}, {{ $TotalLiterGas }}],
          chart: { type: 'pie', height: 350 },
          labels: ['Gasoline', 'Diesel', 'Gas'],
          colors: ['#FFB64D', '#546E7A', '#E91E63'],
          legend: { position: 'bottom' },
          responsive: [{
              breakpoint: 480,
              options: { chart: { width: 200 } }
          }]
      }).render();

      // Debt Timeline
      const timelineData = @json($debtTimeline);
      const debtTimeline = new ApexCharts(document.querySelector("#debt-timeline"), {
          series: [{
              name: 'Total Debt',
              data: timelineData.map(item => item.total)
          }, {
              name: 'Paid',
              data: timelineData.map(item => item.paid)
          }, {
              name: 'Remaining',
              data: timelineData.map(item => item.remaining)
          }],
          chart: { type: 'area', height: 350 },
          colors: ['#3F51B5', '#4CAF50', '#FF9800'],
          xaxis: {
              categories: timelineData.map(item => `${item.year}-${item.month}`),
              type: 'datetime'
          },
          stroke: { curve: 'smooth' },
          tooltip: { x: { format: 'MMM yyyy' } }
      }).render();

      // Monthly Fuel Consumption
      const fuelMonthly = @json($fuelMonthly);
      const fuelTypes = [...new Set(fuelMonthly.map(item => item.type_fuel))];

      const fuelSeries = fuelTypes.map(type => ({
          name: type,
          data: fuelMonthly
              .filter(item => item.type_fuel === type)
              .map(item => item.total_liters)
      }));

      const fuelTimeline = new ApexCharts(document.querySelector("#fuel-timeline"), {
          series: fuelSeries,
          chart: { type: 'line', height: 350 },
          colors: ['#FFB64D', '#546E7A', '#E91E63'],
          xaxis: {
              categories: [...new Set(fuelMonthly.map(item => `${item.year}-${item.month}`))],
              type: 'datetime'
          },
          stroke: { curve: 'smooth' },
          tooltip: { x: { format: 'MMM yyyy' } }
      }).render();
  });
</script>
@endsection

@section('page-script')
<script>
    // ApexCharts initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Debt Progress Chart
        const debtProgress = new ApexCharts(document.querySelector("#debt-progress"), {
            series: [{{ ($TotalPaidDebt/$TotalDebt)*100 }}],
            chart: { height: 100, type: 'radialBar' },
            plotOptions: { radialBar: { hollow: { size: '65%' } } },
            colors: ['#7367F0'],
            labels: ['Paid Debt']
        }).render();

        // Fuel Type Chart
        const fuelChart = new ApexCharts(document.querySelector("#fuel-chart"), {
            series: [{{ $getTotalLiterTypeDiesl }}, {{ $TotalLiterGasoline }}, {{ $TotalLiterGas }}],
            chart: { type: 'donut', height: 300 },
            labels: ['Diesel', 'Gasoline', 'Gas'],
            colors: ['#00CFE8', '#FF9F43', '#EA5455'],
            legend: { position: 'bottom' }
        }).render();
    });
</script>
@endsection

@section('content')
<div class="row g-4">
    <!-- Summary Cards -->
    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1">{{ number_format($TotalDebt, 2) }}</h2>
                                <span>{{ __('Total Debt') }}</span>
                            </div>
                            <i class='bx bx-credit-card bx-lg'></i>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small">
                                <span>{{ __('Paid') }}</span>
                                <span>{{ number_format($TotalPaidDebt, 2) }}</span>
                            </div>
                            <div class="progress bg-dark bg-opacity-25 mt-1" style="height: 4px;">
                                <div class="progress-bar bg-white"
                                     style="width: {{ ($TotalPaidDebt/$TotalDebt)*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1">{{ number_format($TotalFuel, 2) }}</h2>
                                <span>{{ __('Total Fuel') }}</span>
                            </div>
                            <i class='bx bx-gas-pump bx-lg'></i>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small">
                                <span>{{ __('Liters') }}</span>
                                <span>{{ number_format($TotalLiter, 2) }}</span>
                            </div>
                            <div id="fuel-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1">{{ number_format($TotalLiter, 2) }}</h2>
                                <span>{{ __('Total Liters') }}</span>
                            </div>
                            <i class='bx bx-water bx-lg'></i>
                        </div>
                        <div class="mt-3">
                            <div class="row text-center">
                                <div class="col-4 border-end">
                                    <div class="font-weight-bold text-xs">Diesel</div>
                                    <div class="text-sm">{{ number_format($getTotalLiterTypeDiesl, 2) }}</div>
                                </div>
                                <div class="col-4 border-end">
                                    <div class="font-weight-bold text-xs">Gasoline</div>
                                    <div class="text-sm">{{ number_format($TotalLiterGasoline, 2) }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="font-weight-bold text-xs">Gas</div>
                                    <div class="text-sm">{{ number_format($TotalLiterGas, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1">{{ number_format($TotalAmountTypeDiesel + $TotalAmountGasoline, 2) }}</h2>
                                <span>{{ __('Fuel Costs') }}</span>
                            </div>
                            <i class='bx bx-dollar bx-lg'></i>
                        </div>
                        <div class="mt-3">
                            <div id="debt-progress"></div>
                            <div class="text-center small mt-2">
                                {{ number_format(($TotalPaidDebt/$TotalDebt)*100, 1) }}% Paid
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">{{ __('Financial Overview') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="border p-3 mb-3 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">{{ __('Total Debt') }}</div>
                                    <h3 class="mb-0">{{ number_format($TotalDebt, 2) }}</h3>
                                </div>
                                <div class="avatar bg-primary text-white p-3 rounded-circle">
                                    <i class='bx bx-money'></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="border p-3 mb-3 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small">{{ __('Fuel Expenses') }}</div>
                                    <h3 class="mb-0">{{ number_format($TotalFuel, 2) }}</h3>
                                </div>
                                <div class="avatar bg-warning text-dark p-3 rounded-circle">
                                    <i class='bx bx-gas-pump'></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card bg-gradient-dark border-0 text-white">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 col-md-3 text-center">
                                        <div class="text-muted small">{{ __('Diesel Cost') }}</div>
                                        <h4 class="mb-0">{{ number_format($TotalAmountTypeDiesel, 2) }}</h4>
                                        <div class="text-success small">
                                            <i class='bx bx-up-arrow-alt'></i> {{ number_format(($TotalAmountTypeDiesel/$TotalFuel)*100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 text-center">
                                        <div class="text-muted small">{{ __('Gasoline Cost') }}</div>
                                        <h4 class="mb-0">{{ number_format($TotalAmountGasoline, 2) }}</h4>
                                        <div class="text-warning small">
                                            <i class='bx bx-down-arrow-alt'></i> {{ number_format(($TotalAmountGasoline/$TotalFuel)*100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 text-center">
                                        <div class="text-muted small">{{ __('Paid Fuel') }}</div>
                                        <h4 class="mb-0">{{ number_format($TotalPaidFuel, 2) }}</h4>
                                        <div class="text-info small">
                                            <i class='bx bx-trending-up'></i> {{ number_format(($TotalPaidFuel/$TotalFuel)*100, 1) }}%
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 text-center">
                                        <div class="text-muted small">{{ __('Unpaid Fuel') }}</div>
                                        <h4 class="mb-0">{{ number_format($TotalUnPaidFuel, 2) }}</h4>
                                        <div class="text-danger small">
                                            <i class='bx bx-trending-down'></i> {{ number_format(($TotalUnPaidFuel/$TotalFuel)*100, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Statistics -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">{{ __('Quick Stats') }}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class='bx bx-water text-info me-2'></i>
                            {{ __('Diesel Liters') }}
                        </div>
                        <span class="badge bg-info">{{ number_format($getTotalLiterTypeDiesl, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class='bx bx-zap text-warning me-2'></i>
                            {{ __('Gasoline Liters') }}
                        </div>
                        <span class="badge bg-warning">{{ number_format($TotalLiterGasoline, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class='bx bx-wind text-danger me-2'></i>
                            {{ __('Gas Liters') }}
                        </div>
                        <span class="badge bg-danger">{{ number_format($TotalLiterGas, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class='bx bx-check-circle text-success me-2'></i>
                            {{ __('Paid Debt') }}
                        </div>
                        <span class="badge bg-success">{{ number_format($TotalPaidDebt, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class='bx bx-error-circle text-danger me-2'></i>
                            {{ __('Outstanding Debt') }}
                        </div>
                        <span class="badge bg-danger">{{ number_format($TotalRestDebt, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Fuel Cost Distribution') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">{{ __('Refresh') }}</a></li>
                        <li><a class="dropdown-item" href="#">{{ __('Export') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-info me-3">
                                <i class='bx bx-oil'></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Diesel Cost') }}</div>
                                <h5 class="mb-0">{{ number_format($TotalAmountTypeDiesel, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-warning me-3">
                                <i class='bx bx-gas-pump'></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Gasoline Cost') }}</div>
                                <h5 class="mb-0">{{ number_format($TotalAmountGasoline, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-success me-3">
                                <i class='bx bx-check'></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Paid Fuel') }}</div>
                                <h5 class="mb-0">{{ number_format($TotalPaidFuel, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-label-danger me-3">
                                <i class='bx bx-x'></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Unpaid Fuel') }}</div>
                                <h5 class="mb-0">{{ number_format($TotalUnPaidFuel, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12 col-lg-6 mb-4">
      <div class="card">
          <div class="card-header">
              <h5 class="card-title mb-0">Debt Progress</h5>
          </div>
          <div class="card-body">
              <div id="debt-progress"></div>
          </div>
      </div>
  </div>

  <div class="col-12 col-lg-6 mb-4">
      <div class="card">
          <div class="card-header">
              <h5 class="card-title mb-0">Fuel Type Distribution</h5>
          </div>
          <div class="card-body">
              <div id="fuel-chart"></div>
          </div>
      </div>
  </div>

  <div class="col-12 mb-4">
      <div class="card">
          <div class="card-header">
              <h5 class="card-title mb-0">Debt Timeline</h5>
          </div>
          <div class="card-body">
              <div id="debt-timeline"></div>
          </div>
      </div>
  </div>

  <div class="col-12 mb-4">
      <div class="card">
          <div class="card-header">
              <h5 class="card-title mb-0">Monthly Fuel Consumption</h5>
          </div>
          <div class="card-body">
              <div id="fuel-timeline"></div>
          </div>
      </div>
  </div>
</div>
@endsection