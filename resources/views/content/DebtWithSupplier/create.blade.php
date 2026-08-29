
<!-- Modal Create Supplier Debt -->
<div class="modal fade" id="modalAddDebt" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-transparent border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bxs-truck fs-6"></i>
          </div>
          <h5 class="modal-title mb-0 fw-semibold">{{ __('Add Supplier Debt Record') }}</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('debt-supplier.store') }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <!-- 1. Supplier & Customer Details -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label for="tractor_driver_id" class="form-label text-muted small fw-semibold">{{ __('Tractor Driver / Supplier') }} <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bxs-truck text-muted"></i></span>
                <select class="form-select" id="tractor_driver_id" name="tractor_driver_id" required>
                  <option value="">{{ __('Choose a Supplier...') }}</option>
                  @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->fullname }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <label for="fullname" class="form-label text-muted small fw-semibold">{{ __('Customer Name') }} <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
                <input type="text" id="fullname" name="fullname" class="form-control @error('fullname') is-invalid @enderror" placeholder="{{ __('Enter customer name') }}" required />
              </div>
              @error('fullname')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="phone" class="form-label text-muted small fw-semibold">{{ __('Phone Number') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-phone text-muted"></i></span>
                <input type="tel" id="phone" name="phone" class="form-control phone-mask @error('phone') is-invalid @enderror" placeholder="0655 44 33 22" />
              </div>
              @error('phone')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="date_debut_debt" class="form-label text-muted small fw-semibold">{{ __('First Debt Date') }} <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
                <input type="date" id="date_debut_debt" name="date_debut_debt" class="form-control @error('date_debut_debt') is-invalid @enderror" min="2020-01-01" value="{{ $dateToday }}" required />
              </div>
              @error('date_debut_debt')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label for="note" class="form-label text-muted small fw-semibold">{{ __('Notes / Remarks') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-comment text-muted"></i></span>
                <textarea name="note" id="note" class="form-control" rows="1" placeholder="{{ __('Optional notes...') }}"></textarea>
              </div>
            </div>
          </div>

          <!-- 2. Product Items -->
          <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
            <h6 class="mb-0 fw-semibold text-heading d-flex align-items-center gap-1">
              <i class="bx bx-cube-alt text-primary"></i>
              <span>{{ __('Delivered Materials & Products') }}</span>
            </h6>
            <button type="button" id="add-product-create" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
              <i class="bx bx-plus"></i>
              <span>{{ __('Add Item') }}</span>
            </button>
          </div>

          <div id="product-container-create">
            <div class="row g-2 product-row-create align-items-end mb-2">
              <div class="col-md-3">
                <label for="name-product" class="form-label text-muted small fw-semibold">{{ __('Product') }}</label>
                <select id="name-product" class="form-select name-product" name="name_product[]" required>
                  <option value="">{{ __('Choose product...') }}</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
              <div id="inpute-create" class="col-md-3 inpute-create"></div>
              <div class="col-md-3">
                <label for="amount_due" class="form-label text-muted small fw-semibold">{{ __('Amount (DZ)') }}</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text bg-lighter"><i class="bx bx-money text-muted"></i></span>
                  <input type="number" class="form-control" name="amount_due[]" placeholder="1000" required>
                  <span class="input-group-text">.00</span>
                </div>
              </div>
              <div class="col-md-3">
                <label for="date_debut_debt" class="form-label text-muted small fw-semibold">{{ __('Date') }}</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text bg-lighter"><i class='bx bx-calendar text-muted'></i></span>
                  <input type="date" name="date_debt[]" class="form-control" min="2020-01-01" value="{{ $dateToday }}" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-transparent border-top pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-save"></i>
            <span>{{ __('Save Debt') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



