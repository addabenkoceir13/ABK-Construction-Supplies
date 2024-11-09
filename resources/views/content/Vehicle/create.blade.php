<!-- Modal -->
<div class="modal fade" id="modalAddVehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCenterTitle">{{ __('Add Vehicle') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('services.vehicle.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nameWithTitle" class="form-label">{{ __('Name vehicle') }}</label>
                <input type="text" id="nameWithTitle" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Enter Name vehicle') }}">
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="type" class="form-label">{{ __('Type vehicle') }}</label>
                  <select id="type" class="form-select" name="type" >
                    <option value="">{{ __('Choose a type vehicle') }}</option>
                    <option value="car">{{ __('Car') }}</option>
                    <option value="truck">{{ __('Truck') }}</option>
                    <option value="motorcycle">{{ __('Motorcycle') }}</option>
                  </select>
                  @error('type')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="model" class="form-label">{{ __('License plate vehicle') }}</label>
                <div class="row g-0">
                  <div class="col-md-3">
                    <input type="number"  name="wilaya_license" min="0" class="form-control @error('wilaya_license') is-invalid @enderror" placeholder="02">
                  </div>
                  <div class="col-md-1 text-center"><h1>-</h1></div>
                  <div class="col-md-3">
                    <input type="number"  name="year_license" min="0" class="form-control @error('year_license') is-invalid @enderror" placeholder="524">
                  </div>
                  <div class="col-md-1 text-center"><h1>-</h1></div>
                  <div class="col-md-4">
                    <input type="number"  name="license" min="0" class="form-control @error('license') is-invalid @enderror" placeholder="001236">
                  </div>
                </div>
                @error('wilaya_license')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @error('year_license')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @error('license')
                  <div class="alert alert-danger">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="start_date" class="form-label">{{ __('Date of insurance start') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                  <input type="date" id="start_date" name="start_date"  class="form-control @error('start_date') is-invalid @enderror" min="2020-01-01"  />
                </div>
                @error('start_date')
                  <span class="alert alert-danger" role="alert">
                    {{ $message }}
                  </span>
                @enderror
              </div>
              <div class="col-md-6">
                <label for="end_date" class="form-label">{{ __('Date of insurance end') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                  <input type="date" id="end_date" name="end_date"  class="form-control @error('end_date') is-invalid @enderror" min="2020-01-01"  />
                </div>
                @error('end_date')
                  <span class="alert alert-danger" role="alert">
                    {{ $message }}
                  </span>
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

