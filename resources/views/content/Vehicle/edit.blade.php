<!-- Modal Edit Vehicle -->
<div class="modal fade" id="modalEditVehicle{{ $vehicle->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-edit-alt fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Modify Vehicle') }} - {{ $vehicle->name }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.vehicle.update', $vehicle->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nameWithTitle-{{ $vehicle->id }}" class="form-label text-muted small fw-semibold">{{ __('Vehicle Name / Model') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-car text-muted"></i></span>
                <input type="text" id="nameWithTitle-{{ $vehicle->id }}" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $vehicle->name }}" required>
              </div>
              @error('name')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="type-{{ $vehicle->id }}" class="form-label text-muted small fw-semibold">{{ __('Vehicle Type') }}</label>
              <select id="type-{{ $vehicle->id }}" class="form-select @error('type') is-invalid @enderror" name="type" required>
                <option value="">{{ __('Choose a type vehicle') }}</option>
                <option value="car" {{ $vehicle->type == 'car' ? 'selected' : '' }}>{{ __('Car / Utility') }}</option>
                <option value="truck" {{ $vehicle->type == 'truck' ? 'selected' : '' }}>{{ __('Truck / Camion') }}</option>
                <option value="motorcycle" {{ $vehicle->type == 'motorcycle' ? 'selected' : '' }}>{{ __('Motorcycle / Moto') }}</option>
              </select>
              @error('type')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              @php
                $licenseParts = explode(' - ', $vehicle->license_plate);
                $license = $licenseParts[0] ?? '';
                $year_license = $licenseParts[1] ?? '';
                $wilaya_license = $licenseParts[2] ?? '';
              @endphp
              <label class="form-label text-muted small fw-semibold">{{ __('License Plate (Matricule)') }}</label>
              <div class="d-flex align-items-center gap-2">
                <div style="flex: 2;">
                  <input type="number" name="license" min="0" class="form-control text-center @error('license') is-invalid @enderror" value="{{ $license }}" required>
                  <small class="text-muted text-center d-block">{{ __('Number') }}</small>
                </div>
                <span class="fw-bold fs-5 text-muted">-</span>
                <div style="flex: 1.5;">
                  <input type="number" name="year_license" min="0" class="form-control text-center @error('year_license') is-invalid @enderror" value="{{ $year_license }}" required>
                  <small class="text-muted text-center d-block">{{ __('Year Code') }}</small>
                </div>
                <span class="fw-bold fs-5 text-muted">-</span>
                <div style="flex: 1;">
                  <input type="number" name="wilaya_license" min="0" class="form-control text-center @error('wilaya_license') is-invalid @enderror" value="{{ $wilaya_license }}" required>
                  <small class="text-muted text-center d-block">{{ __('Wilaya') }}</small>
                </div>
              </div>
            </div>

            @foreach ($vehicle->insuranceVehicle as $item)
              @if ($item->insuranceDateExpired())
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold">{{ __('Insurance Start (Expired)') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
                    <input type="date" value="{{ $item->start_date }}" readonly class="form-control bg-light" />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label text-muted small fw-semibold">{{ __('Insurance End (Expired)') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text bg-lighter"><i class='bx bx-calendar-x text-muted'></i></span>
                    <input type="date" value="{{ $item->end_date }}" readonly class="form-control bg-light text-danger" />
                  </div>
                </div>
              @else
                <input type="hidden" name="insurance_id" value="{{ $item->id }}">
                <div class="col-md-6">
                  <label for="start_date-{{ $item->id }}" class="form-label text-muted small fw-semibold">{{ __('Insurance Start Date') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
                    <input type="date" id="start_date-{{ $item->id }}" name="start_date" value="{{ $item->start_date }}" class="form-control @error('start_date') is-invalid @enderror" min="2020-01-01" required />
                  </div>
                  @error('start_date')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="end_date-{{ $item->id }}" class="form-label text-muted small fw-semibold">{{ __('Insurance Expiry Date') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text bg-lighter"><i class='bx bx-calendar-x text-muted'></i></span>
                    <input type="date" id="end_date-{{ $item->id }}" name="end_date" value="{{ $item->end_date }}" class="form-control @error('end_date') is-invalid @enderror" min="2020-01-01" required />
                  </div>
                  @error('end_date')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                  @enderror
                </div>
              @endif
            @endforeach
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-save"></i>
            <span>{{ __('Save Changes') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

