
<!-- Modal -->
<div class="modal fade" id="modalAddDebt" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">{{ __('Add Debt') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('debt.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="tractor_driver_id" value="{{ $supplier->id }}">
            <div class="row g-2">
              <div class="col-md-6 mb-3">
                <label for="fullname" class="form-label">{{ __('Customer Name') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-fullname2" class="input-group-text"><i class="bx bx-user"></i></span>
                  <input type="text" id="fullname-search" name="fullname" class="form-control @error('fullname') is-invalid @enderror" placeholder="{{ __('Enter Name') }}"
                  autocomplete="on" list="listFullName" />
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
                  <input type="tel" id="phone" name="phone"  class="form-control phone-mask @error('phone') is-invalid @enderror" placeholder="0655 44 33 22" />
                </div>
                @error('phone')
                  <span class="alert alert-danger " role="alert">
                    {{ $message }}
                  </span>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="date_debut_debt" class="form-label">{{ __('Date Debut Debt') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                  <input type="date" id="date_debut_debt" name="date_debut_debt"  class="form-control @error('date_debut_debt') is-invalid @enderror" min="2020-01-01" value="{{ $dateToday }}" />
                </div>
                @error('date_debut_debt')
                  <span class="alert alert-danger" role="alert">
                    {{ $message }}
                  </span>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="note"  class="form-label">{{ __('Note') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-message2" class="input-group-text"><i class="bx bx-comment"></i></span>
                  <textarea name="note" id="note" class="form-control" placeholder="{{ __('Write your notes') }}"></textarea>
                </div>
              </div>
            </div>


            <div class="divider divider-primary">
              <div class="divider-text">
                <i class='bx bx-cube-alt'></i>
              </div>
            </div>

            <div id="product-container-create">
              <div class="row g-1 product-row-create">
                <div class="col-md-3 mb-3">
                  <label for="name-product" class="form-label">{{ __('Name Product') }}</label>
                  <select id="name-product" class="form-select name-product" name="name_product[]" required>
                    <option value="">{{ __('Choose a product') }}</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div id="inpute-create" class="col-md-3 mb-3 inpute-create">

                </div>
                <div class="col-md-3 mb-3">
                  <label for="amount_due" class="form-label">{{ __('Amount Due') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text">{{ __('DZ') }}</span>
                    <input type="number" class="form-control" name="amount_due[]" min="0" placeholder="1000"  required>
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="date_debut_debt" class="form-label">{{ __('Date Debut Debt') }}</label>
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                    <input type="date" id="date_debut_debt" name="date_debt[]"  class="form-control" min="2020-01-01" value="{{ $dateToday }}" required>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <button type="button" id="add-product-create" class="btn btn-sm btn-outline-success">{{ __('Add') }}</button>
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



