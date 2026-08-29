<!-- Modal Add Insurance Date -->
<div class="modal fade" id="modalAddDateInsuranceVehicle-{{ $vehicle->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-calendar-plus fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Renew Insurance') }} - {{ $vehicle->name }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.vehicle.added-date', $vehicle->id) }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-muted small fw-semibold">{{ __('Vehicle Name') }}</label>
              <input type="text" class="form-control bg-light" value="{{ $vehicle->name }}" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted small fw-semibold">{{ __('License Plate') }}</label>
              <input type="text" class="form-control bg-light" value="{{ $vehicle->license_plate }}" readonly>
            </div>
            <div class="col-md-6">
              <label for="start_date_add-{{ $vehicle->id }}" class="form-label text-muted small fw-semibold">{{ __('New Insurance Start Date') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
                <input type="date" id="start_date_add-{{ $vehicle->id }}" name="start_date" class="form-control @error('start_date') is-invalid @enderror" min="2020-01-01" required />
              </div>
              @error('start_date')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="end_date_add-{{ $vehicle->id }}" class="form-label text-muted small fw-semibold">{{ __('New Insurance Expiry Date') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar-x text-muted'></i></span>
                <input type="date" id="end_date_add-{{ $vehicle->id }}" name="end_date" class="form-control @error('end_date') is-invalid @enderror" min="2020-01-01" required />
              </div>
              @error('end_date')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-save"></i>
            <span>{{ __('Renew Insurance') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

