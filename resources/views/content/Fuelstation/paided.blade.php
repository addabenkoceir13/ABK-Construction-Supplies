<!-- Modal Mark Fuel Receipt as Paid / Unpaid -->
<div class="modal fade" id="modalPaidedFuelStation-{{ $fuelStation->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-money fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Update Payment Status') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('fuel-stations.status', $fuelStation->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body py-4">
          <div class="mb-3">
            <label for="status-{{ $fuelStation->id }}" class="form-label text-muted small fw-semibold">{{ __('Receipt Payment Status') }}</label>
            <select id="status-{{ $fuelStation->id }}" class="form-select" name="status">
              <option value="paid" {{ $fuelStation->status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
              <option value="unpaid" {{ $fuelStation->status == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
            </select>
          </div>
          <p class="text-muted small mb-0">{{ __('Vehicle:') }} <strong>{{ $fuelStation->vehicle->name ?? '' }}</strong> | {{ __('Amount:') }} <strong class="text-primary">{{ number_format($fuelStation->amount, 2) }} DZ</strong></p>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-check"></i>
            <span>{{ __('Update Status') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


