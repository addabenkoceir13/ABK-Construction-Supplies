<div class="modal fade" id="modalPaidedFuelStation-{{ $fuelStation->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Paid Fuel Receipt') }} | {{ $fuelStation->vehicle->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fuel-stations.status',  $fuelStation->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="col-md-6 mb-3">
            <label for="status" class="form-label">{{ __('Status') }}</label>
              <select id="status" class="form-select" name="status" >
                <option value="paid"   {{ $fuelStation->status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                <option value="unpaid" {{ $fuelStation->status == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
              </select>
              @error('type_fuel')
                <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-outline-success">{{ __('Paid') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>


