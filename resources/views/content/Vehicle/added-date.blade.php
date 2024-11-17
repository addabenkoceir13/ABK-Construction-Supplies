<!-- Modal -->
<div class="modal fade" id="modalAddDateInsuranceVehicle-{{ $vehicle->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">{{ __('Modify Vehicle') }} {{ $vehicle->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.vehicle.added-date', $vehicle->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nameWithTitle" class="form-label">{{ __('Name vehicle') }}</label>
              <input type="text" id="nameWithTitle" style="pointer-events: none;" class="form-control" value="{{ $vehicle->name }}">
            </div>
            <div class="col-md-6 mb-3">
              <label for="type" class="form-label">{{ __('Type vehicle') }}</label>
              @switch($vehicle->type)
                @case('car')
                  <input type="text" style="pointer-events: none;" class="form-control" value="{{ __('Car') }}">
                  @break
                @case('truck')
                  <input type="text" style="pointer-events: none;" class="form-control" value="{{ __('Truck') }}">
                  @break
                @case('motorcycle')
                  <input type="text" style="pointer-events: none;" class="form-control" value="{{ __('Motorcycle') }}">
                  @break
              @endswitch
          </div>
            <div class="col-md-6 mb-3">
              @php
                // Assuming $vehicle->license_plate contains the value "00124 - 118 - 02"
                $licenseParts = explode(' - ', $vehicle->license_plate);

                // Assign each part to a variable, with default values if any part is missing
                $license = $licenseParts[0] ?? '';
                $year_license = $licenseParts[1] ?? '';
                $wilaya_license = $licenseParts[2] ?? '';
              @endphp
              <label for="model" class="form-label">{{ __('License plate vehicle') }}</label>
              <div class="row g-0">
                <div class="col-md-3">
                  <input type="text" style="pointer-events: none;" min="0" class="form-control" value="{{ $wilaya_license }}">
                </div>
                <div class="col-md-1 text-center"><h1>-</h1></div>
                <div class="col-md-3">
                  <input type="text" style="pointer-events: none;" min="0" class="form-control" value="{{ $year_license }}">
                </div>
                <div class="col-md-1 text-center"><h1>-</h1></div>
                <div class="col-md-4">
                  <input type="text" style="pointer-events: none;" min="0" class="form-control" value="{{ $license }}">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="start_date" class="form-label">{{ __('Date of insurance start') }}</label>
              <div class="input-group input-group-merge">
                <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" min="2020-01-01"  />
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

