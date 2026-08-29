@extends('layouts/contentNavbarLayout')

@section('title', __('Executive Dashboard - Analytics'))

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
<style>
  .kpi-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
  }
</style>
@endsection

@section('content')
@php
    $debtPaidPercent = $TotalDebt > 0 ? min(100, round(($TotalPaidDebt / $TotalDebt) * 100, 1)) : 0;
    $fuelPaidPercent = $TotalFuel > 0 ? min(100, round(($TotalPaidFuel / $TotalFuel) * 100, 1)) : 0;
@endphp

<!-- 1. Header Banner -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Management') }} /</span> {{ __('Executive Dashboard') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Overview of company revenues, debt settlements, vehicle fleet, and fuel consumption analytics.') }}</p>
  </div>
  <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
    <div class="bg-white shadow-sm border rounded-pill px-3 py-2 text-muted small d-flex align-items-center gap-2">
      <i class="bx bx-calendar text-primary"></i>
      <span>{{ date('l, d F Y') }}</span>
    </div>
  </div>
</div>

<!-- 2. Primary KPI Stat Cards -->
<div class="row g-3 mb-4">
  <!-- Total Debt -->
  <div class="col-sm-6 col-xl-3 col-12">
    <div class="card shadow-sm border-0 kpi-card h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <span class="text-muted small fw-semibold d-block mb-1">{{ __('Total Customer Debts') }}</span>
            <h4 class="card-title mb-0 fw-bold text-primary">{{ number_format($TotalDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          </div>
          <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
            <i class="bx bx-wallet fs-4"></i>
          </div>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>{{ __('Collected:') }} <strong class="text-success">{{ number_format($TotalPaidDebt, 2) }}</strong></span>
            <span>{{ $debtPaidPercent }}%</span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $debtPaidPercent }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Outstanding Balance -->
  <div class="col-sm-6 col-xl-3 col-12">
    <div class="card shadow-sm border-0 kpi-card h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <span class="text-muted small fw-semibold d-block mb-1">{{ __('Outstanding Balance') }}</span>
            <h4 class="card-title mb-0 fw-bold text-danger">{{ number_format($TotalRestDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          </div>
          <div class="avatar avatar-md bg-label-danger rounded p-2 d-flex align-items-center justify-content-center">
            <i class="bx bx-error-circle fs-4"></i>
          </div>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>{{ __('Unsettled:') }} <strong class="text-danger">{{ number_format($TotalRestDebt, 2) }} DZ</strong></span>
            <span class="text-danger">{{ number_format(100 - $debtPaidPercent, 1) }}%</span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $debtPaidPercent }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Fuel Expenditures -->
  <div class="col-sm-6 col-xl-3 col-12">
    <div class="card shadow-sm border-0 kpi-card h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <span class="text-muted small fw-semibold d-block mb-1">{{ __('Total Fuel Expenses') }}</span>
            <h4 class="card-title mb-0 fw-bold text-warning">{{ number_format($TotalFuel, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          </div>
          <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
            <i class="bx bx-gas-pump fs-4"></i>
          </div>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>{{ __('Paid:') }} <strong class="text-success">{{ number_format($TotalPaidFuel, 2) }}</strong></span>
            <span>{{ $fuelPaidPercent }}%</span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $fuelPaidPercent }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Liters -->
  <div class="col-sm-6 col-xl-3 col-12">
    <div class="card shadow-sm border-0 kpi-card h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <span class="text-muted small fw-semibold d-block mb-1">{{ __('Total Liters Refilled') }}</span>
            <h4 class="card-title mb-0 fw-bold text-info">{{ number_format($TotalLiter, 2) }} <small class="fs-6 text-muted">L</small></h4>
          </div>
          <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
            <i class="bx bx-water fs-4"></i>
          </div>
        </div>
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Diesel: <strong>{{ number_format($getTotalLiterTypeDiesl, 0) }}L</strong></span>
            <span>Essence: <strong>{{ number_format($TotalLiterGasoline, 0) }}L</strong></span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 3. Financial Analytics & Charts Row -->
<div class="row g-4 mb-4">
  <!-- Debt Progress Radial Chart -->
  <div class="col-lg-6 col-12">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-pie-chart-alt fs-6"></i>
          </div>
          <h6 class="card-title mb-0 fw-semibold">{{ __('Debt Settlement Breakdown') }}</h6>
        </div>
        <span class="badge bg-label-success">{{ $debtPaidPercent }}% {{ __('Recovered') }}</span>
      </div>
      <div class="card-body d-flex flex-column justify-content-center">
        <div id="debt-progress-chart" style="min-height: 280px;"></div>
        <div class="row text-center mt-2 g-2">
          <div class="col-6 border-end">
            <span class="text-muted small d-block">{{ __('Paid Debts') }}</span>
            <strong class="text-success fs-6">{{ number_format($TotalPaidDebt, 2) }} DZ</strong>
          </div>
          <div class="col-6">
            <span class="text-muted small d-block">{{ __('Remaining Outstanding') }}</span>
            <strong class="text-danger fs-6">{{ number_format($TotalRestDebt, 2) }} DZ</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Fuel Type Distribution Donut Chart -->
  <div class="col-lg-6 col-12">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-doughnut-chart fs-6"></i>
          </div>
          <h6 class="card-title mb-0 fw-semibold">{{ __('Fuel Volume by Type (Liters)') }}</h6>
        </div>
        <span class="badge bg-label-warning">{{ number_format($TotalLiter, 0) }} {{ __('Total Liters') }}</span>
      </div>
      <div class="card-body d-flex flex-column justify-content-center">
        <div id="fuel-distribution-chart" style="min-height: 280px;"></div>
        <div class="row text-center mt-2 g-2">
          <div class="col-4 border-end">
            <span class="text-muted small d-block">{{ __('Diesel') }}</span>
            <strong class="text-primary fs-6">{{ number_format($getTotalLiterTypeDiesl, 1) }} L</strong>
          </div>
          <div class="col-4 border-end">
            <span class="text-muted small d-block">{{ __('Gasoline') }}</span>
            <strong class="text-warning fs-6">{{ number_format($TotalLiterGasoline, 1) }} L</strong>
          </div>
          <div class="col-4">
            <span class="text-muted small d-block">{{ __('Gas (Sirghaz)') }}</span>
            <strong class="text-danger fs-6">{{ number_format($TotalLiterGas, 1) }} L</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 4. Timelines Row -->
<div class="row g-4 mb-4">
  <!-- Debt Evolution Timeline -->
  <div class="col-lg-6 col-12">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-line-chart fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Debt Dynamics Timeline') }}</h6>
      </div>
      <div class="card-body">
        <div id="debt-timeline-chart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>

  <!-- Monthly Fuel Timeline -->
  <div class="col-lg-6 col-12">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-bar-chart-alt-2 fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Monthly Fuel Consumption Trends') }}</h6>
      </div>
      <div class="card-body">
        <div id="fuel-timeline-chart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- 5. Quick Navigation Shortcuts -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
    <div class="avatar avatar-xs bg-label-secondary rounded p-1 d-flex align-items-center justify-content-center">
      <i class="bx bx-grid-alt fs-6"></i>
    </div>
    <h6 class="card-title mb-0 fw-semibold">{{ __('Management Shortcuts') }}</h6>
  </div>
  <div class="card-body py-4">
    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('debt.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bx-user fs-2 text-primary mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Customer Debts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('debt-supplier.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bxs-truck fs-2 text-info mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Supplier Debts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('fuel-stations.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bx-gas-pump fs-2 text-warning mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Fuel Receipts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.building-materials.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bx-cube-alt fs-2 text-success mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Building Materials') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.vehicle.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bx-car fs-2 text-danger mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Vehicle Fleet') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.tractor-driver.index') }}" class="card text-center p-3 border text-decoration-none kpi-card shadow-sm h-100">
          <i class="bx bx-user-pin fs-2 text-dark mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Tractor Drivers') }}</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1. Debt Settlement Donut Chart
  const debtDonutOptions = {
    series: [{{ $TotalPaidDebt }}, {{ $TotalRestDebt }}],
    labels: ['{{ __("Paid Debt") }}', '{{ __("Remaining Debt") }}'],
    chart: {
      type: 'donut',
      height: 280,
      fontFamily: 'inherit'
    },
    colors: ['#28c76f', '#ea5455'],
    stroke: { width: 0 },
    legend: { position: 'bottom' },
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          labels: {
            show: true,
            total: {
              show: true,
              label: '{{ __("Total") }}',
              formatter: () => '{{ number_format($TotalDebt, 0) }} DZ'
            }
          }
        }
      }
    },
    tooltip: {
      y: { formatter: (val) => val.toLocaleString() + ' DZ' }
    }
  };
  new ApexCharts(document.querySelector("#debt-progress-chart"), debtDonutOptions).render();

  // 2. Fuel Distribution Pie Chart
  const fuelPieOptions = {
    series: [{{ $getTotalLiterTypeDiesl }}, {{ $TotalLiterGasoline }}, {{ $TotalLiterGas }}],
    labels: ['{{ __("Diesel") }}', '{{ __("Gasoline") }}', '{{ __("Gas") }}'],
    chart: {
      type: 'pie',
      height: 280,
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#ff9f43', '#ea5455'],
    stroke: { width: 0 },
    legend: { position: 'bottom' },
    tooltip: {
      y: { formatter: (val) => val.toLocaleString() + ' Liters' }
    }
  };
  new ApexCharts(document.querySelector("#fuel-distribution-chart"), fuelPieOptions).render();

  // 3. Debt Evolution Timeline Area Chart
  const rawTimeline = @json($debtTimeline);
  const timelineCategories = rawTimeline.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`);
  const timelineSeries = [
    {
      name: '{{ __("Total Debt") }}',
      data: rawTimeline.map(item => item.total)
    },
    {
      name: '{{ __("Paid") }}',
      data: rawTimeline.map(item => item.paid)
    },
    {
      name: '{{ __("Remaining") }}',
      data: rawTimeline.map(item => item.remaining)
    }
  ];

  const debtTimelineOptions = {
    series: timelineSeries,
    chart: {
      type: 'area',
      height: 300,
      toolbar: { show: false },
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#28c76f', '#ea5455'],
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.05 } },
    xaxis: { categories: timelineCategories },
    yaxis: { labels: { formatter: (val) => val.toLocaleString() } },
    tooltip: { y: { formatter: (val) => val.toLocaleString() + ' DZ' } }
  };
  new ApexCharts(document.querySelector("#debt-timeline-chart"), debtTimelineOptions).render();

  // 4. Monthly Fuel Consumption Chart
  const rawFuelMonthly = @json($fuelMonthly);
  const fuelMonths = [...new Set(rawFuelMonthly.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`))];
  const fuelTypes = [...new Set(rawFuelMonthly.map(item => item.type_fuel))];

  const fuelMonthlySeries = fuelTypes.map(type => ({
    name: type,
    data: fuelMonths.map(month => {
      const entry = rawFuelMonthly.find(item => `${item.year}-${String(item.month).padStart(2, '0')}` === month && item.type_fuel === type);
      return entry ? entry.total_liters : 0;
    })
  }));

  const fuelTimelineOptions = {
    series: fuelMonthlySeries.length > 0 ? fuelMonthlySeries : [{ name: 'Liters', data: [] }],
    chart: {
      type: 'bar',
      height: 300,
      toolbar: { show: false },
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#ff9f43', '#ea5455'],
    plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
    xaxis: { categories: fuelMonths },
    yaxis: { labels: { formatter: (val) => val.toLocaleString() } },
    tooltip: { y: { formatter: (val) => val.toLocaleString() + ' L' } }
  };
  new ApexCharts(document.querySelector("#fuel-timeline-chart"), fuelTimelineOptions).render();
});
</script>
@endsection