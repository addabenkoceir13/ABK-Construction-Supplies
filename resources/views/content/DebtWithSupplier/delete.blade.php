<!-- Modal Delete Supplier Debt -->
<div class="modal fade" id="modalDeleteDebt{{ $debt->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-trash fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Delete Supplier Debt Record') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('debt-supplier.destroy', $debt->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body py-4">
          <div class="d-flex align-items-start gap-3">
            <div class="avatar bg-label-danger rounded p-2 flex-shrink-0">
              <i class="bx bx-error-alt fs-4"></i>
            </div>
            <div>
              <h6 class="mb-1">{{ __('Are you sure you want to delete this supplier debt?') }}</h6>
              <p class="text-muted small mb-0">{{ __('Customer:') }} <strong class="text-danger">{{ $debt->fullname }}</strong> | {{ __('Supplier:') }} <strong>{{ $debt->tractorDriver->fullname ?? '-' }}</strong> | {{ __('Total:') }} <strong>{{ number_format($debt->total_debt_amount, 2) }} DZ</strong>. {{ __('This will remove all associated products and payment histories.') }}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-danger d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-trash"></i>
            <span>{{ __('Delete Debt') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


