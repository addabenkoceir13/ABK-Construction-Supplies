<!-- Modal for edit building -->
<div class="modal fade" id="modalEditBuilding-{{ $category->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-edit-alt fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Modify Building Material') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.building-materials.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="mb-3">
            <label for="nameWithTitle-{{ $category->id }}" class="form-label text-muted small fw-semibold">{{ __('Material Name') }}</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text bg-lighter"><i class="bx bx-package text-muted"></i></span>
              <input type="text" id="nameWithTitle-{{ $category->id }}" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $category->name }}" required>
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
            <span>{{ __('Save Changes') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for delete building -->
<div class="modal fade" id="modalDeleteBuilding-{{ $category->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-trash fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Delete Building Material') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('services.building-materials.destroy', $category->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body py-4">
          <div class="d-flex align-items-start gap-3">
            <div class="avatar bg-label-danger rounded p-2 flex-shrink-0">
              <i class="bx bx-error-alt fs-4"></i>
            </div>
            <div>
              <h6 class="mb-1">{{ __('Do you really want to delete this material?') }}</h6>
              <p class="text-muted small mb-0">{{ __('Material:') }} <strong class="text-danger">{{ $category->name }}</strong>. {{ __('This action cannot be undone.') }}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-danger d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-trash"></i>
            <span>{{ __('Delete Material') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
