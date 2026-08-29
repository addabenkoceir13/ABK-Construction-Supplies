
<!-- Modal Edit Tractor Driver -->
<div class="modal fade" id="modalditTractorDriver{{ $tractorDriver->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-edit-alt fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Modify Tractor Driver') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.tractor-driver.update', $tractorDriver->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="fullname-edit-{{ $tractorDriver->id }}" class="form-label text-muted small fw-semibold">{{ __('Driver Name') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
                <input type="text" id="fullname-edit-{{ $tractorDriver->id }}" name="fullname" class="form-control @error('fullname') is-invalid @enderror" value="{{ $tractorDriver->fullname }}" required />
              </div>
              @error('fullname')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="phone-edit-{{ $tractorDriver->id }}" class="form-label text-muted small fw-semibold">{{ __('Phone Number') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-phone text-muted"></i></span>
                <input type="tel" id="phone-edit-{{ $tractorDriver->id }}" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $tractorDriver->phone }}" required />
              </div>
              @error('phone')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
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



