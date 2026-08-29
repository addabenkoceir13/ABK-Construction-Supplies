<!-- Modal Add Fuel Receipt -->
<div class="modal fade" id="modalAddFuelReceipt" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-gas-pump fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Add Fuel Receipt') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fuel-stations.store') }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="vehicle_id" class="form-label text-muted small fw-semibold">{{ __('Vehicle') }}</label>
              <select id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" name="vehicle_id" required>
                <option value="">{{ __('Choose a vehicle') }}</option>
                @foreach ($vehicles as $vehicle)
                  <option value="{{ $vehicle->id }}">{{ $vehicle->name }} | {{ $vehicle->license_plate }}</option>
                @endforeach
              </select>
              @error('vehicle_id')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="type_fuel" class="form-label text-muted small fw-semibold">{{ __('Fuel Type') }}</label>
              <select id="type_fuel" class="form-select @error('type_fuel') is-invalid @enderror" name="type_fuel" required>
                <option value="">{{ __('Select fuel type') }}</option>
                <option value="Diesel">{{ __('Diesel (Gasoil)') }}</option>
                <option value="gasoline">{{ __('Gasoline (Essence)') }}</option>
                <option value="gas">{{ __('Gas (GPL/Sirghaz)') }}</option>
              </select>
              @error('type_fuel')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="name_owner" class="form-label text-muted small fw-semibold">{{ __('Station Owner / Vendor') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-store text-muted"></i></span>
                <input type="text" id="name_owner" name="name_owner" class="form-control @error('name_owner') is-invalid @enderror" placeholder="{{ __('Station name') }}">
              </div>
              @error('name_owner')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="name_driver" class="form-label text-muted small fw-semibold">{{ __('Driver Name') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
                <input type="text" id="name_driver" name="name_driver" class="form-control @error('name_driver') is-invalid @enderror" placeholder="{{ __('Driver name') }}">
              </div>
              @error('name_driver')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label for="name_distributor" class="form-label text-muted small fw-semibold">{{ __('Distributor / Pump Attendant') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-id-card text-muted"></i></span>
                <input type="text" id="name_distributor" name="name_distributor" class="form-control @error('name_distributor') is-invalid @enderror" placeholder="{{ __('Distributor name') }}">
              </div>
              @error('name_distributor')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label text-muted small fw-semibold">{{ __('Filing Datetime') }}</label>
              <input type="datetime-local" name="filing_datetime" class="form-control @error('filing_datetime') is-invalid @enderror">
              @error('filing_datetime')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label text-muted small fw-semibold">{{ __('Liters') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-water text-muted"></i></span>
                <input type="number" min="0" step="0.01" name="liter" class="form-control @error('liter') is-invalid @enderror" placeholder="0.00" required>
              </div>
              @error('liter')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label text-muted small fw-semibold">{{ __('Total Amount (DZ)') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-money text-muted"></i></span>
                <input type="number" min="0" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" required>
              </div>
              @error('amount')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-save"></i>
            <span>{{ __('Save Receipt') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

