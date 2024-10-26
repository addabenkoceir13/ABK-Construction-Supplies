
<!-- Modal -->
<div class="modal fade" id="modalditSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">{{ __('Add delivery driver') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="row">
            <div class="row g-2">
              <div class="col-md-6 mb-3">
                <label for="fullname" class="form-label">{{ __('Customer Name') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-fullname2" class="input-group-text"><i class="bx bx-user"></i></span>
                  <input type="text" id="fullname-search" name="fullname" class="form-control @error('fullname') is-invalid @enderror" placeholder="{{ __('Enter Name') }}"
                  autocomplete="on" list="listFullName" value="{{ $supplier->fullname }}" />
                  <datalist id="listFullName"></datalist>
                </div>
                @error('fullname')
                  <span class="alert alert-danger " role="alert">
                    {{ $message }}
                  </span>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-phone2" class="input-group-text"><i class="bx bx-phone"></i></span>
                  <input dir="rtl" type="tel" id="phone" name="phone" max="10"  class="form-control phone-mask @error('phone') is-invalid @enderror" placeholder="0655 44 33 22" value="{{ $supplier->phone }}" />
                </div>
                @error('phone')
                  <span class="alert alert-danger " role="alert">
                    {{ $message }}
                  </span>
                @enderror
              </div>
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



