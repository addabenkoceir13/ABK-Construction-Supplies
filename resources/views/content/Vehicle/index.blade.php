@extends('layouts/contentNavbarLayout')

@section('title', __('Vehicle Fleet'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Services') }} /</span> {{ __('Vehicle Fleet') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Manage company vehicles, trucks, licenses, and insurance renewal dates.') }}</p>
  </div>
  <div class="d-flex gap-2 mt-2 mt-sm-0">
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddVehicle">
      <i class="bx bx-plus-circle"></i>
      <span>{{ __('Add Vehicle') }}</span>
    </button>
  </div>
</div>

<!-- Fleet KPI Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Total Fleet') }}</span>
          <h4 class="card-title mb-0 fw-bold text-primary">{{ $vehicles->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-car fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Trucks') }}</span>
          <h4 class="card-title mb-0 fw-bold text-warning">{{ $vehicles->where('type', 'truck')->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bxs-truck fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Cars / Utility') }}</span>
          <h4 class="card-title mb-0 fw-bold text-info">{{ $vehicles->where('type', 'car')->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-car fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Motorcycles') }}</span>
          <h4 class="card-title mb-0 fw-bold text-secondary">{{ $vehicles->where('type', 'motorcycle')->count() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-secondary rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-cycling fs-4"></i>
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
    <h6 class="card-title mb-0 fw-semibold">{{ __('Filter Fleet') }}</h6>
  </div>
  <div class="card-body pt-2">
    <div class="row g-3 align-items-end">
      <div class="col-md-5">
        <label for="vehicle-search" class="form-label text-muted small fw-semibold mb-1">{{ __('Search Fleet') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text bg-lighter"><i class="bx bx-search text-muted"></i></span>
          <input type="text" id="vehicle-search" class="form-control" placeholder="{{ __('Search vehicle name, model, license plate...') }}">
        </div>
      </div>
      <div class="col-md-4">
        <label for="vehicleTypeFilter" class="form-label text-muted small fw-semibold mb-1">{{ __('Vehicle Type') }}</label>
        <select id="vehicleTypeFilter" class="form-select">
          <option value="">{{ __('All Types') }}</option>
          <option value="car">{{ __('Car / Utility') }}</option>
          <option value="truck">{{ __('Truck / Camion') }}</option>
          <option value="motorcycle">{{ __('Motorcycle / Moto') }}</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="button" id="reset-vehicle-filter" class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-1">
          <i class="bx bx-refresh"></i>
          <span>{{ __('Reset') }}</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- 2. Vehicles Table Card -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-car fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Vehicle Fleet List') }}</h5>
        <small class="text-muted">{{ __('Total Vehicles:') }} <strong class="text-primary">{{ $vehicles->count() }}</strong></small>
      </div>
    </div>
  </div>

  @include('content.Vehicle.create')

  <div class="table-responsive text-nowrap">
    <table id="datatable-vehicles" class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th><i class="bx bx-car me-1 text-primary"></i>{{ __('Vehicle Name') }}</th>
          <th class="text-center"><i class="bx bx-cube me-1 text-primary"></i>{{ __('Type') }}</th>
          <th><i class="bx bx-barcode me-1 text-primary"></i>{{ __('License Plate') }}</th>
          <th class="text-center"><i class="bx bx-calendar-shield me-1 text-primary"></i>{{ __('Insurance') }}</th>
          <th class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($vehicles as $vehicle)
          <tr data-type="{{ $vehicle->type }}">
            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                  @switch($vehicle->type)
                    @case('car')
                      <i class="bx bx-car"></i>
                      @break
                    @case('truck')
                      <i class="bx bxs-truck"></i>
                      @break
                    @case('motorcycle')
                      <i class="bx bx-cycling"></i>
                      @break
                    @default
                      <i class="bx bx-car"></i>
                  @endswitch
                </div>
                <span class="fw-semibold text-heading">{{ $vehicle->name }}</span>
              </div>
            </td>
            <td class="text-center">
              @switch($vehicle->type)
                @case('car')
                  <span class="badge bg-label-info rounded-pill px-3 py-2">
                    <i class='bx bx-car me-1'></i>{{ __('Car') }}
                  </span>
                  @break
                @case('truck')
                  <span class="badge bg-label-warning rounded-pill px-3 py-2">
                    <i class='bx bxs-truck me-1'></i>{{ __('Truck') }}
                  </span>
                  @break
                @case('motorcycle')
                  <span class="badge bg-label-secondary rounded-pill px-3 py-2">
                    <i class='bx bx-cycling me-1'></i>{{ __('Motorcycle') }}
                  </span>
                  @break
              @endswitch
            </td>
            <td>
              <span class="badge bg-light text-dark border px-3 py-2 fw-semibold font-monospace">
                {{ $vehicle->license_plate }}
              </span>
            </td>
            <td class="text-center">
              @if ($vehicle->insuranceDateExpiredLast())
                <span class="badge bg-label-danger rounded-pill px-3 py-2" data-bs-toggle="tooltip" title="{{ __('Insurance has expired! Please renew.') }}">
                  <i class="bx bx-error-alt me-1"></i>{{ __('Expired') }}
                </span>
              @else
                <span class="badge bg-label-success rounded-pill px-3 py-2">
                  <i class="bx bx-check-circle me-1"></i>{{ __('Active') }}
                </span>
              @endif
            </td>
            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditVehicle{{ $vehicle->id }}" title="{{ __('Modify Vehicle') }}">
                  <i class="bx bx-edit-alt"></i>
                </button>
                @if ($vehicle->insuranceDateExpiredLast())
                  <button type="button" class="btn btn-icon btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalAddDateInsuranceVehicle-{{ $vehicle->id }}" title="{{ __('Renew insurance') }}">
                    <i class="bx bx-calendar-plus"></i>
                  </button>
                @endif
                <button type="button" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteVehicle{{ $vehicle->id }}" title="{{ __('Delete Vehicle') }}">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
              @include('content.Vehicle.edit')
              @include('content.Vehicle.delete')
              @include('content.Vehicle.added-date')
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bx bx-car fs-2 d-block mb-1"></i>
              {{ __('No vehicles found') }}
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
  $('#vehicle-search').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('#datatable-vehicles tbody tr').filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  $('#vehicleTypeFilter').on('change', function() {
    var type = $(this).val();
    if (!type) {
      $('#datatable-vehicles tbody tr').show();
      return;
    }
    $('#datatable-vehicles tbody tr').filter(function() {
      $(this).toggle($(this).data('type') === type);
    });
  });

  $('#reset-vehicle-filter').on('click', function() {
    $('#vehicle-search').val('');
    $('#vehicleTypeFilter').val('');
    $('#datatable-vehicles tbody tr').show();
  });
});
</script>
@endsection




