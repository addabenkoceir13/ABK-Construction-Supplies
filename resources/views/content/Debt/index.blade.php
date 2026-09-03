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
      function updateProductRowsUI() {
          let count = $('#product-container-create .product-row-create').length;
          $('#modal-product-counter').text(count);

          $('#product-container-create .product-row-create').each(function(index) {
              $(this).find('.row-num').text(index + 1);
              if (count === 1) {
                  $(this).find('.remove-row-create').prop('disabled', true).addClass('opacity-50');
              } else {
                  $(this).find('.remove-row-create').prop('disabled', false).removeClass('opacity-50');
              }
          });
      }

      function updateLiveTotal() {
          let total = 0;
          $('#product-container-create .amount-due-input').each(function() {
              let val = parseFloat($(this).val()) || 0;
              total += val;
          });
          $('#modal-live-total-display').html(total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' <span class="fs-6 fw-bold live-total-currency">DZ</span>');
      }

      // Live total on amount input
      $(document).on('input', '.amount-due-input', function() {
          updateLiveTotal();
      });

      // Function to add new product row in Create Modal
      $('#add-product-create').click(function() {
          let newIndex = $('#product-container-create .product-row-create').length + 1;
          let productRowCreate = `
          <div class="product-row-create product-item-card p-3 rounded-3 border position-relative" data-row-index="${newIndex}">
              <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-bottom-divider">
                <span class="badge rounded-pill px-2 py-1 fs-tiny fw-semibold item-badge-num">
                  <i class="bx bx-cube-alt text-primary me-1"></i>{{ __('المادة رقم') }} <span class="row-num">${newIndex}</span>
                </span>
                <button type="button" class="btn btn-icon btn-xs btn-outline-danger remove-row-create rounded-circle" title="{{ __('حذف هذه المادة') }}">
                  <i class="bx bx-trash fs-6"></i>
                </button>
              </div>

              <div class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('اسم المنتوج / الفئة') }} <span class="text-danger">*</span></label>
                  <select class="form-select name-product custom-select-styled" name="name_product[]" required>
                    <option value="">{{ __('اختر المنتوج...') }}</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 col-lg-3 inpute-create">
                </div>
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('المبلغ المستحق') }} <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge custom-input-group">
                    <span class="input-group-text border-end-0 text-success fw-bold">DZ</span>
                    <input type="number" class="form-control border-start-0 ps-1 amount-due-input" name="amount_due[]" min="0" step="0.01" placeholder="1000" required>
                    <span class="input-group-text text-muted">.00</span>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('تاريخ الدين') }} <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge custom-input-group">
                    <span class="input-group-text border-end-0"><i class='bx bx-calendar text-muted'></i></span>
                    <input type="date" name="date_debt[]" class="form-control border-start-0 ps-1" min="2020-01-01" value="{{ $dateToday }}" required>
                  </div>
                </div>
              </div>
          </div>`;

          let $row = $(productRowCreate);
          $('#product-container-create').append($row);
          updateProductRowsUI();
          updateLiveTotal();
      });

      // Function to remove product row with animation
      $(document).on('click', '.remove-row-create', function() {
          let $row = $(this).closest('.product-row-create');
          let count = $('#product-container-create .product-row-create').length;
          if (count > 1) {
              $row.addClass('product-card-removing');
              setTimeout(function() {
                  $row.remove();
                  updateProductRowsUI();
                  updateLiveTotal();
              }, 250);
          }
      });

      // Initial setup
      updateProductRowsUI();
      updateLiveTotal();

      $(document).on('change', '#name-product, .name-product', function() {
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
                            <label for="quantity" class="form-label text-muted small fw-semibold mb-1">{{ __('الكمية / القياس') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge custom-input-group">
                              <input type="number" id="quantity" step="0.01" name="quantity[]" class="form-control border-start-0 ps-1" min="0" placeholder="{{ __('أدخل الكمية') }}" required>
                              <span class="input-group-text unit-addon text-primary fw-semibold">${response.data[0].name}</span>
                            </div>
                        </div>
                    `;
                    container.append(InputCreate);
                } else {
                    let datas = response.data;
                    let InputCreate = `
                        <input type="hidden" name="subcategory_ids[]" class="subcategory_id" value="">
                        <label class="form-label text-muted small fw-semibold mb-1">{{ __('الكمية / النوع') }} <span class="text-danger">*</span></label>
                        <select class="form-select subcategory-select custom-select-styled" name="quantity[]" required>
                            <option value="">{{ __('اختر النوع...') }}</option>
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

