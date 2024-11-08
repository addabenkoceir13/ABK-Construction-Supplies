@extends('layouts/contentNavbarLayout')

@section('title', __('Debts'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light"><a href="{{ route('debt.index') }}">{{ __('Debts') }}</a> / </span>
  {{ __('Modify Debt') }}
</h4>

<div class="col-12">
  <div class="card mb-4">
    <h5 class="card-header">{{ __('Modify Debt') }}</h5>
    <div class="card-body">
      <form action="{{ route('debt-supplier.update', $debt->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="row">
            <div class="row g-2">
              <div class="col-md-6 mb-3">
                <label for="fullname" class="form-label">{{ __('Customer Name') }}</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-fullname2" class="input-group-text"><i class="bx bx-user"></i></span>
                  <input type="text" id="fullname" name="fullname" class="form-control @error('fullname') is-invalid @enderror" value="{{ $debt->fullname }}" />
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
                  <input dir="rtl" type="tel" id="phone" name="phone"  class="form-control phone-mask @error('phone') is-invalid @enderror" value="{{ $debt->phone }}" />
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
                  <input dir="rtl" type="date" id="date_debut_debt" name="date_debut_debt"  class="form-control @error('date_debut_debt') is-invalid @enderror" min="2020-01-01" value="{{ $debt->date_debut_debt }}" />
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
                  <textarea name="note" id="note" class="form-control" placeholder="{{ __('Write your notes') }}">{{ $debt->note }}</textarea>
                </div>
              </div>
            </div>

            <div class="divider divider-primary">
              <div class="divider-text"><i class='bx bx-cube-alt'></i></div>
            </div>

            <div id="product-container-edit-{{ $debt->id }}">
              <div class="row g-1 product-row-edit">
                  @foreach ($debt->getDebtProduct  as $item)
                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                    <div class="col-md-3 mb-3">
                      <label for="name-product" class="form-label">{{ __('Name Product') }}</label>
                      <select id="name-product-edit-{{ $item->id }}" class="form-select name-product" name="name_product[]" required>
                        <option value="{{ $item->name_category }}" selected>{{ $item->name_category }}</option>
                        @foreach ($categories as $category)
                          <option value="{{ $category->name }}" data-id="{{ $category->id }}" >{{ $category->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3 mb-3">
                      <div class="col-md-12" id="empty-quantity-{{ $item->id }}">
                        <input type="hidden"  name="subcategory_ids[]" value="{{ $item->subcategory_id  }}" >
                        <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                        <input type="text" id="quantity" step="0.01" name="quantity[]" class="form-control" style="pointer-events: none;" min="0" value="{{ $item->quantity }}" >
                      </div>
                      <div id="inpute-edit-{{ $item->id }}" class="inpute-edit col-md-12">
                      </div>
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="amount_due" class="form-label">{{ __('Amount Due') }}</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text">{{ __('DZ') }}</span>
                        <input type="number" class="form-control" name="amount_due[]" min="0" value="{{ $item->amount }}" required>
                        <span class="input-group-text">.00</span>
                      </div>
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="date_debut_debt" class="form-label">{{ __('Date Debut Debt') }}</label>
                      <div class="input-group input-group-merge">
                        <span id="basic-icon-default-phone2" class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                        <input type="date" id="date_debut_debt" name="date_debt[]"  class="form-control" min="2020-01-01" value="{{ $item->date_debt }}" required>
                      </div>
                    </div>
                  @endforeach
              </div>
            </div>

            <div>
              <button type="button" id="add-product-edit-{{ $debt->id }}"  class="btn btn-sm btn-outline-success">{{ __('Add') }}</button>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
