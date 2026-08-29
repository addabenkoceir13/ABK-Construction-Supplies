
<!-- Modal Add Tractor Driver -->
<div class="modal fade" id="modalAddTractorDriver" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-user-plus fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Add Tractor Driver') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.tractor-driver.store') }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="fullname-create" class="form-label text-muted small fw-semibold">{{ __('Driver Name') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
                <input type="text" id="fullname-create" name="fullname" class="form-control @error('fullname') is-invalid @enderror" placeholder="{{ __('Enter driver full name') }}" required autocomplete="off" />
              </div>
              @error('fullname')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="phone-create" class="form-label text-muted small fw-semibold">{{ __('Phone Number') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-phone text-muted"></i></span>
                <input type="tel" id="phone-create" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0655 44 33 22" required autocomplete="off" />
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
            <span>{{ __('Save Driver') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



