@extends('layouts/contentNavbarLayout')

@section('title', __('Modify Debt') . ' - ' . $debt->fullname)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light"><a href="{{ route('debt.index') }}" class="text-muted text-decoration-none">{{ __('Customer Debts') }}</a> /</span> {{ __('Modify Debt') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Update customer debt dossier, note, products list, and amounts.') }}</p>
  </div>
  <div class="d-flex gap-2 mt-2 mt-sm-0">
    <a href="{{ route('debt.show', $debt->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
      <i class="bx bx-show"></i>
      <span>{{ __('View Dossier') }}</span>
    </a>
    <a href="{{ route('debt.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
      <i class="bx bx-arrow-back"></i>
      <span>{{ __('Back to List') }}</span>
    </a>
  </div>
</div>

<form action="{{ route('debt.update', $debt->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PATCH')

  <!-- 1. Customer Information Card -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
      <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-user fs-6"></i>
      </div>
      <h6 class="card-title mb-0 fw-semibold">{{ __('Customer Information') }}</h6>
    </div>
    <div class="card-body pt-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="fullname" class="form-label text-muted small fw-semibold">{{ __('Customer Name') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
            <input type="text" id="fullname" name="fullname" class="form-control @error('fullname') is-invalid @enderror" value="{{ $debt->fullname }}" required />
          </div>
          @error('fullname')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="phone" class="form-label text-muted small fw-semibold">{{ __('Phone Number') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-phone text-muted"></i></span>
            <input type="tel" id="phone" name="phone" class="form-control phone-mask @error('phone') is-invalid @enderror" value="{{ $debt->phone }}" />
          </div>
          @error('phone')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="date_debut_debt" class="form-label text-muted small fw-semibold">{{ __('Date First Debt') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class='bx bx-calendar-check text-muted'></i></span>
            <input type="date" id="date_debut_debt" name="date_debut_debt" class="form-control @error('date_debut_debt') is-invalid @enderror" min="2020-01-01" value="{{ $debt->date_debut_debt }}" required />
          </div>
          @error('date_debut_debt')
            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label for="note" class="form-label text-muted small fw-semibold">{{ __('Notes / Remarks') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-comment text-muted"></i></span>
            <textarea name="note" id="note" class="form-control" rows="1" placeholder="{{ __('Write your notes') }}">{{ $debt->note }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Products & Materials Card -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-cube-alt fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Debt Products & Materials') }}</h6>
      </div>
      <button type="button" id="add-product-edit-{{ $debt->id }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
        <i class="bx bx-plus"></i>
        <span>{{ __('Add Item') }}</span>
      </button>
    </div>
    <div class="card-body pt-4">
      <div id="product-container-edit-{{ $debt->id }}">
        <div class="row g-2 product-row-edit">
          @foreach ($debt->getDebtProduct as $item)
            <input type="hidden" name="id[]" value="{{ $item->id }}">
            <div class="col-md-3 mb-3">
              <label class="form-label text-muted small fw-semibold">{{ __('Product / Material') }}</label>
              <select id="name-product-edit-{{ $item->id }}" class="form-select name-product" name="name_product[]" required>
                <option value="{{ $item->name_category }}" selected>{{ $item->name_category }}</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <div class="col-md-12" id="empty-quantity-{{ $item->id }}">
                <input type="hidden" name="subcategory_ids[]" value="{{ $item->subcategory_id }}">
                <label for="quantity" class="form-label text-muted small fw-semibold">{{ __('Quantity') }}</label>
                <input type="text" id="quantity" step="0.01" name="quantity[]" class="form-control" style="pointer-events: none;" min="0" value="{{ $item->quantity }}">
              </div>
              <div id="inpute-edit-{{ $item->id }}" class="inpute-edit col-md-12"></div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label text-muted small fw-semibold">{{ __('Amount Due (DZ)') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class="bx bx-money text-muted"></i></span>
                <input type="number" class="form-control" name="amount_due[]" min="0" value="{{ $item->amount }}" required>
                <span class="input-group-text">.00</span>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label text-muted small fw-semibold">{{ __('Date Debt') }}</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text bg-lighter"><i class='bx bx-calendar text-muted'></i></span>
                <input type="date" name="date_debt[]" class="form-control" min="2020-01-01" value="{{ $item->date_debt }}" required>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-end gap-2">
      <a href="{{ route('debt.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
      <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
        <i class="bx bx-save"></i>
        <span>{{ __('Save Changes') }}</span>
      </button>
    </div>
  </div>
</form>
@endsection

@section('page-script')

{{-- ! js for model modify (edit.blade.php) in order to add inputs --}}
<script>
  $(document).ready(function() {
      // Function to add new product row
      $(document).on('click', '[id^=add-product-edit-]', function() {
          let debtId = $(this).attr('id').split('-').pop(); // Extract the debt id from the button's ID
          let productRowEdit = `
          <div class="row g-1 product-row-new-edit">
            <input type="hidden" name="id[]" value="0">
              <div class="col-md-3 mb-3">
                  <label class="form-label">{{ __('Name Product') }}</label>
                  <select id="name-product" class="form-select name-product" name="name_product[]" required>
                      <option value="">{{ __('Choose a product') }}</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div id="inpute-new-edit" class="col-md-3 mb-3 inpute-new-edit">
              </div>
              <div class="col-md-3 mb-3">
                  <label class="form-label">{{ __('Amount Due') }}</label>
                  <div class="input-group input-group-merge">
                      <span class="input-group-text">{{ __('DZ') }}</span>
                      <input type="text" class="form-control" name="amount_due[]" placeholder="100" required>
                      <span class="input-group-text">.00</span>
                  </div>
              </div>
              <div class="col-md-3 mb-3">
                  <label class="form-label">{{ __('Date Debut Debt') }}</label>
                  <div class="input-group input-group-merge">
                    <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                    <input type="date" name="date_debt[]"  class="form-control" min="2020-01-01" value="{{ $dateToday }}" required>
                  </div>
              </div>
              <div class="col-md-12 mb-3">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-row-edit">{{ __('Delete') }}</button>
              </div>
          </div>`;

          $('#product-container-edit-' + debtId).append(productRowEdit);
      });

      // Function to remove product row
      $(document).on('click', '.remove-row-edit', function() {
          $(this).closest('.product-row-edit').remove();
      });

      $(document).on('change', '[id^=name-product-edit-]', function() {
          let debtId = $(this).attr('id').split('-').pop(); // Extract the debt id from the button's ID
          var selectedOption = $(this).find('option:selected');
          var id = selectedOption.data('id');
          var name = $(this).val();


          $(this).closest('.product-row-edit').find('#inpute-edit-'+ debtId).empty();

          $.ajax({
            url: '{{ route('services.subcategory.show', '01') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'GET',
            data: { id: id },
            dataType: 'JSON',
            success:function(response){
              $(this).closest('.product-row-edit').find('#empty-quantity-'+ debtId).remove();
                if (response.data[0].input_type == 'number') {
                    let InputCreate = `
                        <div>
                            <input type="hidden"  name="subcategory_ids[]" value="${response.data[0].id}" >
                            <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                            <div class="input-group input-group-merge">
                              <input  type="number" id="quantity" step="0.01" name="quantity[]" class="form-control" min="0" placeholder="{{ __('Enter Quantity') }}" required>
                              <span class="input-group-text">${response.data[0].name}</span>
                            </div>
                        </div>
                    `;
                    $(this).closest('.product-row-edit').find('#inpute-edit-'+ debtId).append(InputCreate);
                } else {
                    let datas = response.data;
                    let InputCreate = `
                        <input type="hidden"  name="subcategory_ids[]"  class="subcategory_id-${debtId}" id="subcategory_id-${debtId}" >
                        <label class="form-label">{{ __('Quantity') }}</label>
                        <select class="form-select" name="quantity[]" id="subcategory-${debtId}" required>
                            <option value="">{{ __('Choose a quantity') }}</option>
                    `;
                    for (let index = 0; index < datas.length; index++) {
                        InputCreate += `<option value="${datas[index].name}" data-id="${datas[index].id}" >${datas[index].name}</option>`;
                    }
                    InputCreate += `</select>`;
                    $(this).closest('.product-row-edit').find('#inpute-edit-'+ debtId).append(InputCreate);
                }
            }.bind(this) // Bind the correct context for "this"
          });

      });

      $(document).on('change', '[id^=subcategory-]', function() {
        let debtId = $(this).attr('id').split('-').pop(); // Extract the debt id from the button's ID
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        var name = $(this).val();
        $(this).closest('.product-row-edit').find('#subcategory_id-'+ debtId).val(id);
      });


      $(document).on('change', '#name-product', function() {
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        var name = $(this).val();

        // Clear the existing inputs before adding new ones
        $(this).closest('.product-row-new-edit').find('.inpute-new-edit').empty();

        $.ajax({
          url: '{{ route('services.subcategory.show', '01') }}',
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'GET',
          data: { id: id },
          dataType: 'JSON',
          success:function(response){
              if (response.data[0].input_type == 'number') {
                  let InputCreate = `
                      <div>
                          <input type="hidden"  name="subcategory_ids[]" value="${response.data[0].id}" >
                          <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                          <div class="input-group input-group-merge">
                            <input  type="number" id="quantity" step="0.01" name="quantity[]" class="form-control" min="0" placeholder="{{ __('Enter Quantity') }}" required>
                            <span class="input-group-text">${response.data[0].name}</span>
                          </div>
                      </div>
                  `;
                  $(this).closest('.product-row-new-edit').find('.inpute-new-edit').append(InputCreate);
              } else {
                  let datas = response.data;
                  let InputCreate = `
                      <input type="hidden"  name="subcategory_ids[]"  class="subcategory_id" id="subcategory_id" value="">
                      <label class="form-label">{{ __('Quantity') }}</label>
                      <select class="form-select" name="quantity[]" id="subcategory" required>
                          <option value="">{{ __('Choose a quantity') }}</option>
                  `;
                  for (let index = 0; index < datas.length; index++) {
                      InputCreate += `<option value="${datas[index].name}" data-id="${datas[index].id}" >${datas[index].name}</option>`;
                  }
                  InputCreate += `</select>`;
                  $(this).closest('.product-row-new-edit').find('.inpute-new-edit').append(InputCreate);
              }
          }.bind(this) // Bind the correct context for "this"
        });
      });

      $(document).on('change', '#subcategory', function() {
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        var name = $(this).val();
        $(this).closest('.product-row-new-edit').find('.subcategory_id').val(id);
      });

  });
</script>
@endsection
