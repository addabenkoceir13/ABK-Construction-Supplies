<!-- Modal Add Vehicle -->
<div class="modal fade" id="modalAddVehicle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-car fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Add New Vehicle') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.vehicle.store') }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nameWithTitle" class="form-label text-muted small fw-semibold">{{ __('Vehicle Name / Model') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-car text-muted"></i></span>
                <input type="text" id="nameWithTitle" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('e.g. Toyota Hilux, Hyundai H100') }}" required>
              </div>
              @error('name')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="type" class="form-label text-muted small fw-semibold">{{ __('Vehicle Type') }}</label>
              <select id="type" class="form-select @error('type') is-invalid @enderror" name="type" required>
                <option value="">{{ __('Choose a type vehicle') }}</option>
                <option value="car">{{ __('Car / Utility') }}</option>
                <option value="truck">{{ __('Truck / Camion') }}</option>
                <option value="motorcycle">{{ __('Motorcycle / Moto') }}</option>
              </select>
              @error('type')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label class="form-label text-muted small fw-semibold">{{ __('License Plate (Matricule)') }}</label>
              <div class="d-flex align-items-center gap-2">
                <div style="flex: 2;">
                  <input type="number" name="license" min="0" class="form-control text-center @error('license') is-invalid @enderror" placeholder="001236" required>
                  <small class="text-muted text-center d-block">{{ __('Number') }}</small>
                </div>
                <span class="fw-bold fs-5 text-muted">-</span>
                <div style="flex: 1.5;">
                  <input type="number" name="year_license" min="0" class="form-control text-center @error('year_license') is-invalid @enderror" placeholder="118" required>
                  <small class="text-muted text-center d-block">{{ __('Year Code') }}</small>
                </div>
                <span class="fw-bold fs-5 text-muted">-</span>
                <div style="flex: 1;">
                  <input type="number" name="wilaya_license" min="0" class="form-control text-center @error('wilaya_license') is-invalid @enderror" placeholder="02" required>
                  <small class="text-muted text-center d-block">{{ __('Wilaya') }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label for="start_date" class="form-label text-muted small fw-semibold">{{ __('Insurance Start Date') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
                <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" min="2020-01-01" required />
              </div>
              @error('start_date')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="end_date" class="form-label text-muted small fw-semibold">{{ __('Insurance Expiry Date') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar-x text-muted'></i></span>
                <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" min="2020-01-01" required />
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
            <span>{{ __('Save Vehicle') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
