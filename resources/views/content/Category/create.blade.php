<!-- Modal Add Building Material -->
<div class="modal fade" id="modalAddBuilding" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-cube-alt fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Add Building Material') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.building-materials.store') }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="mb-3">
            <label for="nameWithTitle" class="form-label text-muted small fw-semibold">{{ __('Material / Category Name') }}</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text bg-lighter"><i class="bx bx-package text-muted"></i></span>
              <input type="text" id="nameWithTitle" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Enter material name (e.g. Ciment, Sable...)') }}" required autofocus autocomplete="off">
            </div>
            @error('name')
              <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-save"></i>
            <span>{{ __('Save Material') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
