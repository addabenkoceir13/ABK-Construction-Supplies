@extends('layouts/contentNavbarLayout')

@section('title', __('Executive Analytics Dashboard'))

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
<style>
  .kpi-stat-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border-radius: 0.75rem;
    overflow: hidden;
  }
  .kpi-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(115, 103, 240, 0.15) !important;
  }
  .chart-card {
    border-radius: 0.75rem;
  }
  .radial-score-container {
    min-height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .quick-action-card {
    transition: all 0.2s ease;
    border-radius: 0.75rem;
  }
  .quick-action-card:hover {
    transform: translateY(-3px);
    background-color: rgba(115, 103, 240, 0.05);
  }
  .avatar-stat {
    width: 48px;
    height: 48px;
  }
</style>
@endsection

@section('content')
<!-- 1. Executive Dashboard Header & Overview Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
  <div>
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <span class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-pie-chart-alt-2 fs-5"></i>
      </span>
      <span>{{ __('Executive Analytics & Business Intelligence') }}</span>
    </h4>
    <p class="text-muted mb-0 small">
      {{ __('Real-time financial performance, debt collection tracking, fuel consumption, and fleet logistics.') }}
    </p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="window.location.reload();">
      <i class="bx bx-refresh"></i>
      <span>{{ __('Refresh Data') }}</span>
    </button>
    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="window.print();">
      <i class="bx bx-printer"></i>
      <span>{{ __('Export / Print Report') }}</span>
    </button>
  </div>
</div>

<!-- 2. Primary Financial KPIs (4 Stat Cards) -->
<div class="row g-3 mb-4">
  <!-- Card 1: Total Debts -->
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 kpi-stat-card h-100">
      <div class="card-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">{{ __('Total Debt Incurred') }}</span>
          <div class="avatar avatar-stat bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
            <i class="bx bx-wallet fs-4"></i>
          </div>
        </div>
        <div>
          <h4 class="card-title mb-1 fw-bold text-primary">{{ number_format($totalDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          <div class="d-flex flex-wrap gap-1 small mt-2">
            <span class="badge bg-label-secondary font-monospace">{{ $totalDebtsCount }} {{ __('Records') }}</span>
            <span class="badge bg-label-info">{{ __('Cust:') }} {{ number_format($customerTotalDebt, 0) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 2: Total Paid Debts -->
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 kpi-stat-card h-100">
      <div class="card-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">{{ __('Total Paid & Collected') }}</span>
          <div class="avatar avatar-stat bg-label-success rounded-3 d-flex align-items-center justify-content-center">
            <i class="bx bx-check-double fs-4"></i>
          </div>
        </div>
        <div>
          <h4 class="card-title mb-1 fw-bold text-success">{{ number_format($totalPaidDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          <div class="d-flex align-items-center gap-2 small mt-2">
            <span class="badge bg-label-success rounded-pill px-2">
              <i class="bx bx-trending-up me-1"></i>{{ $recoveryRate }}% {{ __('Recovery') }}
            </span>
            <span class="text-muted small">{{ $paidDebtsCount }} {{ __('Settled') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 3: Outstanding Debt Balance -->
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 kpi-stat-card h-100">
      <div class="card-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">{{ __('Outstanding Debt Balance') }}</span>
          <div class="avatar avatar-stat bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
            <i class="bx bx-time-five fs-4"></i>
          </div>
        </div>
        <div>
          <h4 class="card-title mb-1 fw-bold text-danger">{{ number_format($totalRestDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          <div class="d-flex flex-wrap gap-1 small mt-2">
            <span class="badge bg-label-danger font-monospace">{{ $unpaidDebtsCount }} {{ __('Pending') }}</span>
            <span class="badge bg-label-warning">{{ __('Supplier:') }} {{ number_format($supplierRestDebt, 0) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card 4: Total Fuel Expense -->
  <div class="col-sm-6 col-xl-3">
    <div class="card shadow-sm border-0 kpi-stat-card h-100">
      <div class="card-body p-3 d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">{{ __('Total Fuel Accounting') }}</span>
          <div class="avatar avatar-stat bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
            <i class="bx bx-gas-pump fs-4"></i>
          </div>
        </div>
        <div>
          <h4 class="card-title mb-1 fw-bold text-warning">{{ number_format($totalFuelAmount, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
          <div class="d-flex align-items-center gap-2 small mt-2">
            <span class="badge bg-label-info font-monospace">{{ number_format($totalLiters, 1) }} L</span>
            <span class="badge bg-label-danger">{{ $fuelUnpaidReceiptsCount }} {{ __('Unpaid') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 3. Primary Charts Section (Area Timeline & Gauge) -->
<div class="row g-4 mb-4">
  <!-- Area Chart: Debt Timeline Evolution -->
  <div class="col-12 col-xl-8">
    <div class="card shadow-sm border-0 chart-card h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-line-chart fs-6"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 fw-semibold">{{ __('Debt Dynamics Timeline') }}</h5>
            <small class="text-muted">{{ __('Historical trend of incurred debts, collected payments, and remaining balance.') }}</small>
          </div>
        </div>
        <div class="d-flex gap-1">
          <span class="badge bg-label-primary">{{ __('Total') }}</span>
          <span class="badge bg-label-success">{{ __('Paid') }}</span>
          <span class="badge bg-label-danger">{{ __('Remaining') }}</span>
        </div>
      </div>
      <div class="card-body pt-3">
        <div id="debt-timeline-chart" style="min-height: 320px;"></div>
      </div>
    </div>
  </div>

  <!-- Radial Gauge & Distribution: Debt Settlement Health -->
  <div class="col-12 col-xl-4">
    <div class="card shadow-sm border-0 chart-card h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-tachometer fs-6"></i>
        </div>
        <div>
          <h5 class="card-title mb-0 fw-semibold">{{ __('Settlement Health Rate') }}</h5>
          <small class="text-muted">{{ __('Overall collection efficiency') }}</small>
        </div>
      </div>
      <div class="card-body d-flex flex-column justify-content-between pt-3">
        <div id="debt-gauge-chart" class="radial-score-container"></div>
        <div class="border rounded-3 p-3 bg-light mt-2">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-badge-check text-success"></i> {{ __('Settled Customer Ratio:') }}
            </span>
            <strong class="text-success">{{ number_format($customerPaidDebt, 2) }} DZ</strong>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-error text-danger"></i> {{ __('Pending Supplier Debts:') }}
            </span>
            <strong class="text-danger">{{ number_format($supplierRestDebt, 2) }} DZ</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 4. Secondary Charts Section (Fuel Trends & Fuel Type Donut) -->
<div class="row g-4 mb-4">
  <!-- Bar/Line Chart: Monthly Fuel Trends -->
  <div class="col-12 col-xl-8">
    <div class="card shadow-sm border-0 chart-card h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-bar-chart-alt-2 fs-6"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 fw-semibold">{{ __('Monthly Fuel Consumption Trends') }}</h5>
            <small class="text-muted">{{ __('Monthly volume consumed (Liters) across Diesel, Gasoline, and Gas.') }}</small>
          </div>
        </div>
        <span class="badge bg-label-warning rounded-pill px-3">{{ number_format($totalLiters, 0) }} {{ __('Total Liters') }}</span>
      </div>
      <div class="card-body pt-3">
        <div id="fuel-consumption-chart" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>

  <!-- Donut Chart: Fuel Type Volume Share -->
  <div class="col-12 col-xl-4">
    <div class="card shadow-sm border-0 chart-card h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-doughnut-chart fs-6"></i>
        </div>
        <div>
          <h5 class="card-title mb-0 fw-semibold">{{ __('Fuel Volume by Type (Liters)') }}</h5>
          <small class="text-muted">{{ __('Distribution share across fuel categories') }}</small>
        </div>
      </div>
      <div class="card-body pt-3 d-flex flex-column justify-content-between">
        <div id="fuel-donut-chart"></div>
        <div class="row g-2 mt-2">
          <div class="col-4 text-center">
            <div class="p-2 border rounded-3 bg-light">
              <span class="text-muted small d-block">{{ __('Diesel') }}</span>
              <strong class="text-primary small">{{ number_format($dieselLiters, 0) }} L</strong>
            </div>
          </div>
          <div class="col-4 text-center">
            <div class="p-2 border rounded-3 bg-light">
              <span class="text-muted small d-block">{{ __('Gasoline') }}</span>
              <strong class="text-warning small">{{ number_format($gasolineLiters, 0) }} L</strong>
            </div>
          </div>
          <div class="col-4 text-center">
            <div class="p-2 border rounded-3 bg-light">
              <span class="text-muted small d-block">{{ __('Gas') }}</span>
              <strong class="text-danger small">{{ number_format($gasLiters, 0) }} L</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 5. Top Leaderboards: Top Customer Debts & Top Supplier Debts -->
<div class="row g-4 mb-4">
  <!-- Top Outstanding Customer Debts -->
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-user-voice fs-6"></i>
          </div>
          <h6 class="card-title mb-0 fw-semibold">{{ __('Top Outstanding Customer Debts') }}</h6>
        </div>
        <a href="{{ route('debt.index') }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Customer') }}</th>
              <th class="text-center">{{ __('Phone') }}</th>
              <th class="text-end">{{ __('Remaining Due') }}</th>
              <th class="text-center">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($topCustomerDebts as $custDebt)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                      {{ strtoupper(mb_substr($custDebt->fullname, 0, 1)) }}
                    </div>
                    <div>
                      <span class="fw-semibold text-heading d-block">{{ $custDebt->fullname }}</span>
                      <small class="text-muted">{{ $custDebt->date_debut_debt }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  @if ($custDebt->phone)
                    <a href="tel:{{ $custDebt->phone }}" class="badge bg-label-secondary text-decoration-none">
                      <i class="bx bx-phone me-1"></i>{{ $custDebt->phone }}
                    </a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td class="text-end">
                  <strong class="text-danger">{{ number_format($custDebt->rest_debt_amount, 2) }} DZ</strong>
                </td>
                <td class="text-center">
                  <a href="{{ route('debt.show', $custDebt->id) }}" class="btn btn-icon btn-sm btn-outline-primary" title="{{ __('View Dossier') }}">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">{{ __('No outstanding customer debts.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Outstanding Supplier Debts -->
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bxs-truck fs-6"></i>
          </div>
          <h6 class="card-title mb-0 fw-semibold">{{ __('Top Outstanding Supplier Debts') }}</h6>
        </div>
        <a href="{{ route('debt-supplier.index') }}" class="btn btn-xs btn-outline-primary">{{ __('View All') }}</a>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>{{ __('Customer / Supplier') }}</th>
              <th class="text-center">{{ __('Driver') }}</th>
              <th class="text-end">{{ __('Remaining Due') }}</th>
              <th class="text-center">{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($topSupplierDebts as $supDebt)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-warning rounded-circle d-flex align-items-center justify-content-center fw-bold">
                      {{ strtoupper(mb_substr($supDebt->fullname, 0, 1)) }}
                    </div>
                    <div>
                      <span class="fw-semibold text-heading d-block">{{ $supDebt->fullname }}</span>
                      <small class="text-muted">{{ $supDebt->date_debut_debt }}</small>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-label-info">
                    <i class="bx bxs-truck me-1"></i>{{ $supDebt->tractorDriver->fullname ?? '-' }}
                  </span>
                </td>
                <td class="text-end">
                  <strong class="text-danger">{{ number_format($supDebt->rest_debt_amount, 2) }} DZ</strong>
                </td>
                <td class="text-center">
                  <a href="{{ route('debt-supplier.show', $supDebt->id) }}" class="btn btn-icon btn-sm btn-outline-primary" title="{{ __('View Dossier') }}">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">{{ __('No outstanding supplier debts.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- 6. Fleet & Inventory Operational Summaries (3 Cards Row) -->
<div class="row g-3 mb-4">
  <!-- Vehicle Fleet Logistics -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-car fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Fleet & Vehicle Status') }}</h6>
      </div>
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ __('Total Fleet Vehicles:') }}</span>
          <strong class="text-primary fs-6">{{ $totalVehicles }}</strong>
        </div>
        <div class="row g-2 text-center mb-3">
          <div class="col-4">
            <div class="p-2 border rounded-3 bg-light">
              <i class="bx bxs-truck text-warning fs-5 d-block mb-1"></i>
              <span class="text-muted small d-block">{{ __('Trucks') }}</span>
              <strong class="small">{{ $trucksCount }}</strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 border rounded-3 bg-light">
              <i class="bx bx-car text-info fs-5 d-block mb-1"></i>
              <span class="text-muted small d-block">{{ __('Cars') }}</span>
              <strong class="small">{{ $carsCount }}</strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 border rounded-3 bg-light">
              <i class="bx bx-cycling text-secondary fs-5 d-block mb-1"></i>
              <span class="text-muted small d-block">{{ __('Motos') }}</span>
              <strong class="small">{{ $motoCount }}</strong>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-label-danger">
          <span class="small text-danger fw-semibold d-flex align-items-center gap-1">
            <i class="bx bx-error-circle"></i> {{ __('Expired Insurances:') }}
          </span>
          <span class="badge bg-danger">{{ $expiredInsuranceCount }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Building Materials & Categories -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-cube-alt fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Material Categories & Units') }}</h6>
      </div>
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ __('Main Categories:') }}</span>
          <strong class="text-success fs-6">{{ $totalCategories }}</strong>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ __('Subcategories / Units:') }}</span>
          <strong class="text-heading fs-6">{{ $totalSubcategories }}</strong>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">{{ __('Active Tractor Drivers:') }}</span>
          <strong class="text-info fs-6">{{ $totalDrivers }}</strong>
        </div>
        <a href="{{ route('services.building-materials.index') }}" class="btn btn-sm btn-outline-success w-100 d-flex align-items-center justify-content-center gap-1">
          <i class="bx bx-layer"></i>
          <span>{{ __('Manage Materials Catalog') }}</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Top In-Demand Materials -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-package fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Top Delivered Materials') }}</h6>
      </div>
      <div class="card-body p-3">
        <ul class="list-group list-group-flush">
          @forelse ($topProducts as $prod)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0 border-bottom">
              <div>
                <span class="fw-semibold text-heading small d-block">{{ $prod->name_category }}</span>
                <small class="text-muted">{{ $prod->items_count }} {{ __('Deliveries') }}</small>
              </div>
              <strong class="text-primary small">{{ number_format($prod->total_amount, 0) }} DZ</strong>
            </li>
          @empty
            <li class="list-group-item text-center py-3 text-muted border-0">{{ __('No material delivery data.') }}</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- 7. Quick Operational Navigation Shortcuts -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
      <i class="bx bx-grid-alt fs-6"></i>
    </div>
    <h6 class="card-title mb-0 fw-semibold">{{ __('Operational Quick Access') }}</h6>
  </div>
  <div class="card-body py-4">
    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('debt.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
          <i class="bx bx-user fs-2 text-primary mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Customer Debts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('debt-supplier.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
          <i class="bx bxs-truck fs-2 text-info mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Supplier Debts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('fuel-stations.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
          <i class="bx bx-gas-pump fs-2 text-warning mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Fuel Receipts') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.building-materials.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
          <i class="bx bx-cube-alt fs-2 text-success mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Building Materials') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.vehicle.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
          <i class="bx bx-car fs-2 text-danger mb-2"></i>
          <span class="fw-semibold text-heading small">{{ __('Vehicle Fleet') }}</span>
        </a>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('services.tractor-driver.index') }}" class="card text-center p-3 border text-decoration-none quick-action-card shadow-sm h-100">
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
  // 1. Debt Dynamics Timeline Area Chart
  const rawTimeline = @json($debtTimeline);
  const timelineCategories = rawTimeline.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`);
  const timelineSeries = [
    {
      name: '{{ __("Total Debt Incurred") }}',
      data: rawTimeline.map(item => Number(item.total) || 0)
    },
    {
      name: '{{ __("Paid & Collected") }}',
      data: rawTimeline.map(item => Number(item.paid) || 0)
    },
    {
      name: '{{ __("Remaining Balance") }}',
      data: rawTimeline.map(item => Number(item.remaining) || 0)
    }
  ];

  const debtTimelineOptions = {
    series: timelineSeries,
    chart: {
      type: 'area',
      height: 330,
      toolbar: { show: false },
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#28c76f', '#ea5455'],
    stroke: { curve: 'smooth', width: 2.5 },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.45,
        opacityTo: 0.05,
        stops: [0, 95, 100]
      }
    },
    xaxis: {
      categories: timelineCategories,
      labels: { style: { fontSize: '11px' } }
    },
    yaxis: {
      labels: {
        formatter: (val) => val.toLocaleString() + ' DZ'
      }
    },
    tooltip: {
      y: {
        formatter: (val) => val.toLocaleString() + ' DZ'
      }
    },
    legend: { position: 'top', horizontalAlign: 'right' }
  };
  new ApexCharts(document.querySelector("#debt-timeline-chart"), debtTimelineOptions).render();

  // 2. Radial Gauge: Debt Settlement Health
  const gaugeOptions = {
    series: [{{ $recoveryRate }}],
    chart: {
      height: 240,
      type: 'radialBar'
    },
    plotOptions: {
      radialBar: {
        startAngle: -135,
        endAngle: 135,
        hollow: {
          size: '65%'
        },
        track: {
          background: '#f1f1f1',
          strokeWidth: '100%'
        },
        dataLabels: {
          name: {
            fontSize: '13px',
            color: '#6e6b7b',
            offsetY: -5
          },
          value: {
            offsetY: 5,
            fontSize: '22px',
            fontWeight: '700',
            formatter: (val) => val + '%'
          }
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        type: 'horizontal',
        gradientToColors: ['#28c76f'],
        stops: [0, 100]
      }
    },
    stroke: { dashArray: 4 },
    colors: ['#7367f0'],
    labels: ['{{ __("Settlement Efficiency") }}']
  };
  new ApexCharts(document.querySelector("#debt-gauge-chart"), gaugeOptions).render();

  // 3. Monthly Fuel Consumption Trends (Stacked Bar Chart)
  const rawFuelMonthly = @json($fuelMonthly);
  const fuelMonths = [...new Set(rawFuelMonthly.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`))];
  const fuelTypes = [...new Set(rawFuelMonthly.map(item => item.type_fuel))];

  const fuelMonthlySeries = fuelTypes.map(type => ({
    name: type.toUpperCase(),
    data: fuelMonths.map(month => {
      const entry = rawFuelMonthly.find(item => `${item.year}-${String(item.month).padStart(2, '0')}` === month && item.type_fuel === type);
      return entry ? Number(entry.total_liters) : 0;
    })
  }));

  const fuelTimelineOptions = {
    series: fuelMonthlySeries.length > 0 ? fuelMonthlySeries : [{ name: 'Liters', data: [] }],
    chart: {
      type: 'bar',
      height: 300,
      stacked: true,
      toolbar: { show: false },
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#ff9f43', '#ea5455'],
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '45%'
      }
    },
    xaxis: {
      categories: fuelMonths,
      labels: { style: { fontSize: '11px' } }
    },
    yaxis: {
      labels: {
        formatter: (val) => val.toLocaleString() + ' L'
      }
    },
    tooltip: {
      y: {
        formatter: (val) => val.toLocaleString() + ' Liters'
      }
    },
    legend: { position: 'top', horizontalAlign: 'right' }
  };
  new ApexCharts(document.querySelector("#fuel-consumption-chart"), fuelTimelineOptions).render();

  // 4. Fuel Type Volume Share Donut
  const fuelDonutOptions = {
    series: [{{ $dieselLiters }}, {{ $gasolineLiters }}, {{ $gasLiters }}],
    labels: ['{{ __("Diesel") }}', '{{ __("Gasoline") }}', '{{ __("Gas") }}'],
    chart: {
      type: 'donut',
      height: 250,
      fontFamily: 'inherit'
    },
    colors: ['#7367f0', '#ff9f43', '#ea5455'],
    stroke: { width: 0 },
    legend: { position: 'bottom' },
    plotOptions: {
      pie: {
        donut: {
          size: '68%',
          labels: {
            show: true,
            total: {
              show: true,
              label: '{{ __("Total Liters") }}',
              formatter: () => '{{ number_format($totalLiters, 0) }} L'
            }
          }
        }
      }
    },
    tooltip: {
      y: {
        formatter: (val) => val.toLocaleString() + ' Liters'
      }
    }
  };
  new ApexCharts(document.querySelector("#fuel-donut-chart"), fuelDonutOptions).render();
});
</script>
@endsection