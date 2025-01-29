<!-- Modal -->
<div class="modal fade" id="modalAddFuelReceipt" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">{{ __('Add Fuel Receipt') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fuel-stations.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
                <label for="vehicle_id" class="form-label">{{ __('Type vehicle') }}</label>
                  <select id="vehicle_id" class="form-select" name="vehicle_id" >
                    <option value="">{{ __('Choose a type vehicle') }}</option>
                    @foreach ($vehicles as $vehicle)
                      <option value="{{ $vehicle->id }}">
                        {{ $vehicle->name }} |
                        {{ $vehicle->license_plate }}
                      </option>
                    @endforeach
                  </select>
                  @error('vehicle_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
            <div class="col-md-6 mb-3">
              <label for="name_owner" class="form-label">{{ __('Name Owner') }}</label>
              <input type="text" id="name_owner" name="name_owner" class="form-control @error('name_owner') is-invalid @enderror" placeholder="{{ __('Enter Name Owner') }}">
              @error('name_owner')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="name_driver" class="form-label">{{ __('Name Driver') }}</label>
              <input type="text" id="name_driver" name="name_driver" class="form-control @error('name_driver') is-invalid @enderror" placeholder="{{ __('Enter Name Driver') }}">
              @error('name_driver')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="name_distributor" class="form-label">{{ __('Name Distributor') }}</label>
              <input type="text" id="name_distributor" name="name_distributor" class="form-control @error('name_distributor') is-invalid @enderror" placeholder="{{ __('Enter Name Distributor') }}">
              @error('name_distributor')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Filing Datetime') }}</label>
              <input type="datetime-local"  name="filing_datetime" class="form-control @error('filing_datetime') is-invalid @enderror" placeholder="{{ __('Enter Filing Datetime') }}">
              @error('filing_datetime')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="type_fuel" class="form-label">{{ __('Fuel type') }}</label>
                <select id="type_fuel" class="form-select" name="type_fuel" >
                  <option value="">{{ __('Select fuel type') }}</option>
                  <option value="Diesel">{{ __('Diesel') }}</option>
                  <option value="gasoline">{{ __('Gasoline') }}</option>
                  <option value="gas">{{ __('Gas') }}</option>
                </select>
                @error('type_fuel')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Liter') }}</label>
              <input type="number" min="0" step="0.01"  name="liter" class="form-control @error('liter') is-invalid @enderror" placeholder="{{ __('Enter Liter') }}">
              @error('liter')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">{{ __('Amount') }}</label>
              <input type="number" min="0" step="0.01"  name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="{{ __('Enter amount') }}">
              @error('amount')
                  <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

