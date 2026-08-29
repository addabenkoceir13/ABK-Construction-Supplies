@extends('layouts/contentNavbarLayout')

@section('title', __('Tractor Driver'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Services') }} /</span> {{ __('Tractor Drivers & Delivery') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Manage delivery drivers, tractor operators, and assignment statuses.') }}</p>
  </div>
  <div class="d-flex gap-2 mt-2 mt-sm-0">
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddTractorDriver">
      <i class="bx bx-plus-circle"></i>
      <span>{{ __('Add Tractor Driver') }}</span>
    </button>
  </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-4 col-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Total Drivers') }}</span>
          <h4 class="card-title mb-0 fw-bold text-primary">{{ $tractorDrivers->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-user-pin fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Active Drivers') }}</span>
          <h4 class="card-title mb-0 fw-bold text-success">{{ $tractorDrivers->where('status', 'active')->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-check-circle fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Inactive') }}</span>
          <h4 class="card-title mb-0 fw-bold text-warning">{{ $tractorDrivers->where('status', '!=', 'active')->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-time-five fs-4"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 1. Filter Card -->
<div class="card mb-4 shadow-sm border-0">
  <div class="card-header bg-transparent pb-1 pt-3 d-flex align-items-center gap-2">
    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
      <i class="bx bx-filter-alt fs-6"></i>
    </div>
    <h6 class="card-title mb-0 fw-semibold">{{ __('Filter Drivers') }}</h6>
  </div>
  <div class="card-body pt-2">
    <div class="row g-3 align-items-end">
      <div class="col-md-5">
        <label for="driver-search" class="form-label text-muted small fw-semibold mb-1">{{ __('Search Driver') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text bg-lighter"><i class="bx bx-search text-muted"></i></span>
          <input type="text" id="driver-search" class="form-control" placeholder="{{ __('Search by driver name or phone...') }}">
        </div>
      </div>
      <div class="col-md-4">
        <label for="statusFilter" class="form-label text-muted small fw-semibold mb-1">{{ __('Status') }}</label>
        <select id="statusFilter" class="form-select">
          <option value="">{{ __('All Statuses') }}</option>
          <option value="Active">{{ __('Active') }}</option>
          <option value="Inactive">{{ __('Inactive') }}</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="button" id="reset-driver-filter" class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-1">
          <i class="bx bx-refresh"></i>
          <span>{{ __('Reset') }}</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- 2. Drivers Table Card -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-user-pin fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Tractor Drivers List') }}</h5>
        <small class="text-muted">{{ __('Total Drivers:') }} <strong class="text-primary">{{ $tractorDrivers->count() }}</strong></small>
      </div>
    </div>
  </div>

  @include('content.TractorDriver.create')

  <div class="table-responsive text-nowrap">
    <table id="datatable-drivers" class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th><i class="bx bx-user me-1 text-primary"></i>{{ __('Driver Name') }}</th>
          <th><i class="bx bx-phone me-1 text-primary"></i>{{ __('Phone') }}</th>
          <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Created At') }}</th>
          <th class="text-center"><i class="bx bx-check-shield me-1 text-primary"></i>{{ __('Status') }}</th>
          <th class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($tractorDrivers as $tractorDriver)
          <tr>
            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                  {{ strtoupper(mb_substr($tractorDriver->fullname, 0, 1)) }}
                </div>
                <span class="fw-semibold text-heading">{{ $tractorDriver->fullname }}</span>
              </div>
            </td>
            <td>
              @if($tractorDriver->phone)
                <a href="tel:{{ $tractorDriver->phone }}" class="text-muted d-flex align-items-center gap-1 text-decoration-none">
                  <i class="bx bx-phone fs-7 text-primary"></i>
                  <span>{{ $tractorDriver->phone }}</span>
                </a>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center text-muted small">
                <i class="bx bx-calendar me-1 text-primary"></i>
                <span>{{ $tractorDriver->created_at->format('Y-m-d') }}</span>
              </div>
            </td>
            <td class="text-center">
              @if ($tractorDriver->status == 'active')
                <span class="badge bg-label-success rounded-pill px-3 py-2">
                  <i class="bx bx-check-circle me-1"></i>{{ __('Active') }}
                </span>
              @else
                <span class="badge bg-label-warning rounded-pill px-3 py-2">
                  <i class="bx bx-time-five me-1"></i>{{ __('Inactive') }}
                </span>
              @endif
            </td>
            <td class="text-center">
              @if ($tractorDriver->type == 'delivery')
                <div class="d-flex align-items-center justify-content-center gap-1">
                  <button type="button" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalditTractorDriver{{ $tractorDriver->id }}" title="{{ __('Modify delivery driver') }}">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteTractorDriver{{ $tractorDriver->id }}" title="{{ __('Delete delivery driver') }}">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
                @include('content.TractorDriver.edit')
                @include('content.TractorDriver.deleted')
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
              {{ __('No tractor drivers found') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
<script>
$(document).ready(function() {
  // Client-side instant filter for driver table
  $('#driver-search').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#datatable-drivers tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  $('#statusFilter').on('change', function() {
    var filter = $(this).val();
    if (!filter) {
      $('#datatable-drivers tbody tr').show();
      return;
    }
    $('#datatable-drivers tbody tr').filter(function() {
      var text = $(this).find('td:nth-child(5)').text();
      $(this).toggle(text.indexOf(filter) > -1);
    });
  });

  $('#reset-driver-filter').on('click', function() {
    $('#driver-search').val('');
    $('#statusFilter').val('');
    $('#datatable-drivers tbody tr').show();
  });
});
</script>
@endsection




