@extends('layouts/contentNavbarLayout')

@section('title', __('Debts Supplier'))

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Debts') }} /</span> {{ __('Unpaid Tractor Driver Delivery Debts') }}
</h4>
<!-- Basic Bootstrap Table -->
<div class="card p-2">
  <h5 class="card-header">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDebt">
      {{ __('Add Debt') }}
    </button>
  </h5>
  @include('content.DebtWithSupplier.create')
  <div class="table-responsive text-nowrap">
    {{-- <div class="mb-3 col-md-4">
      <label for="statusFilter" class="form-label">{{ __('Filter by Status') }}</label>
      <select id="statusFilter" class="form-select">
          <option value="">{{ __('All') }}</option>
          <option value="Paid">{{ __('Paid') }}</option>
          <option value="Unpaid">{{ __('Unpaid') }}</option>
      </select>
    </div> --}}
    <table id="datatable-debt" class="table table-hover is-stripedt">
      <thead>
          <tr>
              <th >#</th>
              <th >{{ __('Tractor driver') }}</th>
              <th >{{ __('Name') }}</th>
              <th >{{ __('Phone') }}</th>
              <th >{{ __('Debts') }}</th>
              <th >{{ __('Create At') }}</th>
              <th >{{ __('Status') }}</th>
              <th >{{ __('Action') }}</th>
          </tr>

      </thead>
      <tbody>
        @foreach ($debts as $debt)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $debt->tractorDriver->fullname }}</td>
            <td>{{ $debt->fullname }}</td>
            <td>{{ $debt->phone }}</td>
            <td>
              <table class="table table-sm table-bordered">
                <tbody>
                  @foreach ($debt->getDebtProduct  as $item)
                    <tr  >
                      <td>{{ $item->name_category }}</td>
                      <td>{{ $item->quantity }}</td>
                      <td>{{ $item->amount }} {{ __('DZ') }}</td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show me-1"></i> {{ __('Show') }}</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> {{ __('Edit') }}</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> {{ __('Delete') }}</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                  <tr>
                    <td colspan="2">{{ __('Total') }}</td>
                    <td>{{ $debt->total_debt_amount }} {{ __('DZ') }}</td>
                    <td></td>
                  </tr>
              </tbody>
              </table>

            </td>
            <td>{{ $debt->date_debut_debt }}</td>
            <td>
              @if ($debt->status == 'paid')
                <span class="badge bg-label-success me-1">{{ __('Paid') }}</span>
              @else
                <span class="badge bg-label-warning me-1">{{ __('Unpaid') }}</span>
              @endif
            </td>
            <td>
              <a href="{{ route('debt-supplier.show', $debt->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-show me-1'></i> <span>{{ __('View Debt') }}</span>">
                <span class="badge bg-label-info"><i class='bx bx-show me-1'></i></span>
              </a>
              <a href="{{ route('debt-supplier.edit', $debt->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-edit bx-xs' ></i> <span>{{ __('Modify Debt') }}</span>">
                <span class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteDebt{{ $debt->id }}">
                <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete  debt') }}</span>">
                <i class="bx bx-trash me-1"></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#PayDebtModal{{ $debt->id }}" data-row-id="{{ $debt->id }}">
                <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Pay a debt') }}</span>">
                <i class='bx bx-money'></i></span>
              </a>
              <a href="{{ route('debt.printer-facteur-client',['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" target="_blank" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-show me-1'></i> <span>{{ __('Print Invoice') }}</span>">
                <span class="badge bg-label-dribbble"><i class='bx bx-printer me-1'></i></span>
              </a>
            </td>
          </tr>
          @include('content.DebtWithSupplier.delete')
          @include('content.DebtWithSupplier.payDebt')
        @endforeach
      </tbody>
      <tfoot>
          {{-- <tr>
            <th >#</th>
              <th >{{ __('Name') }}</th>
              <th >{{ __('Phone') }}</th>
              <th >{{ __('Debts') }}</th>
          </tr> --}}
      </tfoot>
    </table>
  </div>
</div>
<!--/ Basic Bootstrap Table -->
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>

<script>
  $(document).ready(function() {

      new DataTable('#datatable-debt', {
        initComplete: function () {
            let api = this.api();

            // Add Status Filter Dropdown
            $('#statusFilter').on('change', function () {
                let language = "{{ app()->getLocale() }}";
                let filterValue = $(this).val();
                console.log(language);
                console.log(filterValue);
                let column = api.column(5);
                if (language === 'ar') {
                  switch (filterValue){
                    case 'Paid':
                      column.search('تم دفع', true, false).draw();
                      break;
                    case 'Unpaid':
                      column.search('لم يدفع', true, false).draw();
                      break;
                    default:
                      column.search('', true, false).draw();
                  }
                } else {
                  if (filterValue) {
                      api.column(5).search('^' + filterValue + '$', true, false).draw();
                  } else {
                      api.column(5).search('').draw();
                  }
                }
            });

            // Initialize text input search on each column footer
            api.columns().every(function () {
                let column = this;
                let title = column.footer() ? column.footer().textContent : '';

                // Create input element if title is present
                if (title) {
                    let input = document.createElement('input');
                    input.placeholder = title;
                    column.footer().replaceChildren(input);

                    // Event listener for input
                    input.addEventListener('keyup', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                }
            });
        }
      });
  });
</script>
{{-- ! js for model created (create.blade.php) in order to add inputs --}}
<script>
  $(document).ready(function() {
      var cpt = 0;
      // Function to add new product row
      $('#add-product-create').click(function() {
          let productRowCreate = `
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
                      <input type="text" class="form-control" name="amount_due[]" placeholder="100" aria-label="Amount (to the nearest DZ)" required>
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
              <div class="col-md-12 mb-3">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-row-create">{{ __('Delete') }}</button>
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
        var name = $(this).val();

        // Clear the existing inputs before adding new ones
        $(this).closest('.product-row-create').find('.inpute-create').empty();

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
                  $(this).closest('.product-row-create').find('.inpute-create').append(InputCreate);
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
                  $(this).closest('.product-row-create').find('.inpute-create').append(InputCreate);
              }
          }.bind(this) // Bind the correct context for "this"
        });
      });

      $(document).on('change', '#subcategory', function() {
        var selectedOption = $(this).find('option:selected');
        var id = selectedOption.data('id');
        var name = $(this).val();
        $(this).closest('.product-row-create').find('.subcategory_id').val(id);

      });



  });
</script>

<script>
  $(document).ready(function () {
    // Initialize total amount
    let totalAmount = 0;
    let currentRowId = null;
    let currentModelId = null;

    // Function to update the displayed total in the modal
    function updateModalTotal() {
      $('.modal-total-amount').text(totalAmount.toFixed(2));
      $('.total-value').val(totalAmount.toFixed(2));
    }
    $('.pay-btn').on('click', function () {
      currentRowId = $(this).data('row-id');
      totalAmount = 0;
    });

    $(document).on('change', '.debt-checkbox', function () {
      currentModelId = $(this).data('row-id');
      const amount = parseFloat($(this).data('amount'));

      if ($(this).is(':checked')) {
        totalAmount += amount;
      } else {
        totalAmount -= amount;
      }
      updateModalTotal();
    });
  });
</script>

@endsection

