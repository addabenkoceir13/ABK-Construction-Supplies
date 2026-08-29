@extends('layouts/contentNavbarLayout')

@section('title', __('Debts'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Debts') }} /</span> {{ __('Unpaid Debts') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Manage and track all customer debts and pending payments.') }}</p>
  </div>
</div>

{{-- 1. Dedicated Search & Filter Card --}}
<x-debt-search-card
  :action="route('debt.index')"
  :name="request('name')"
  :phone="request('phone')"
  :result-count="$debts->total()"
/>

{{-- 2. Dedicated Table Card --}}
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-wallet fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Unpaid Customer Debts') }}</h5>
        <small class="text-muted">{{ __('Total records:') }} <strong class="text-primary">{{ $debts->total() }}</strong></small>
      </div>
    </div>
    <div>
      <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddDebt">
        <i class="bx bx-plus-circle"></i>
        <span>{{ __('Add Debt') }}</span>
      </button>
    </div>
  </div>

  @include('content.Debt.create')

  <div id="debts-table-region">
    @include('content.Debt._debtsTable')
  </div>
</div>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>

{{-- JS for Create Modal dynamic product rows --}}
<script>
  $(document).ready(function() {
      // Function to add new product row in Create Modal
      $('#add-product-create').click(function() {
          let productRowCreate = `
          <div class="row g-1 product-row-create border-top pt-2 mt-2">
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
                      <input type="number" class="form-control" name="amount_due[]" placeholder="100" min="0" aria-label="Amount (to the nearest DZ)" required>
                      <span class="input-group-text">.00</span>
                  </div>
              </div>
              <div class="col-md-3 mb-3">
                  <label for="date_debut_debt" class="form-label">{{ __('Date Debut Debt') }}</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                    <input type="date" name="date_debt[]" class="form-control" min="2020-01-01" value="{{ $dateToday }}" required>
                  </div>
              </div>
              <div class="col-md-12 mb-2 text-end">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-row-create">
                    <i class="bx bx-trash me-1"></i>{{ __('Delete') }}
                  </button>
              </div>
          </div>`;

          $('#product-container-create').append(productRowCreate);
      });

      // Function to remove product row
      $(document).on('click', '.remove-row-create', function() {
          $(this).closest('.product-row-create').remove();
      });

      $(document).on('change', '#name-product', function() {
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        var container = $(this).closest('.product-row-create').find('.inpute-create');

        // Clear existing inputs before adding new ones
        container.empty();

        if (!id) return;

        $.ajax({
          url: '{{ route('services.subcategory.show', '01') }}',
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'GET',
          data: { id: id },
          dataType: 'JSON',
          success: function(response){
              if (response.data && response.data.length > 0) {
                if (response.data[0].input_type == 'number') {
                    let InputCreate = `
                        <div>
                            <input type="hidden" name="subcategory_ids[]" value="${response.data[0].id}">
                            <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                            <div class="input-group input-group-merge">
                              <input type="number" id="quantity" step="0.01" name="quantity[]" class="form-control" min="0" placeholder="{{ __('Enter Quantity') }}" required>
                              <span class="input-group-text">${response.data[0].name}</span>
                            </div>
                        </div>
                    `;
                    container.append(InputCreate);
                } else {
                    let datas = response.data;
                    let InputCreate = `
                        <input type="hidden" name="subcategory_ids[]" class="subcategory_id" value="">
                        <label class="form-label">{{ __('Quantity') }}</label>
                        <select class="form-select subcategory-select" name="quantity[]" required>
                            <option value="">{{ __('Choose a quantity') }}</option>
                    `;
                    for (let index = 0; index < datas.length; index++) {
                        InputCreate += `<option value="${datas[index].name}" data-id="${datas[index].id}">${datas[index].name}</option>`;
                    }
                    InputCreate += `</select>`;
                    container.append(InputCreate);
                }
              }
          }
        });
      });

      $(document).on('change', '.subcategory-select', function() {
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        $(this).closest('.product-row-create').find('.subcategory_id').val(id);
      });

      $('#fullname-search').on('keyup', function() {
          var query = $(this).val();
          if (query.length < 2) return;

          $.ajax({
              url: '{{ route('debt.search') }}',
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              method: 'POST',
              data: {query: query},
              success: function(data){
                  if (data.status) {
                      let suggestions = data.query;
                      let dataList = $('#listFullName');
                      dataList.empty();
                      suggestions.forEach((item) => {
                          let option = $('<option>').val(item.name || item.fullname);
                          dataList.append(option);
                      });
                  }
              }
          });
      });
  });
</script>

{{-- JS for Pay Debt Modal Calculation --}}
<script>
  $(document).ready(function () {
    let totalAmount = 0;

    function updateModalTotal() {
      $('.modal-total-amount').text(totalAmount.toFixed(2) + ' DZ');
      $('.total-value').val(totalAmount.toFixed(2));
    }

    $(document).on('click', '.pay-btn', function () {
      totalAmount = 0;
      updateModalTotal();
    });

    $(document).on('change', '.debt-checkbox', function () {
      const amount = parseFloat($(this).data('amount')) || 0;

      if ($(this).is(':checked')) {
        totalAmount += amount;
      } else {
        totalAmount = Math.max(0, totalAmount - amount);
      }
      updateModalTotal();
    });
  });
</script>
@endsection

