@extends('layouts/contentNavbarLayout')

@section('title', __('Debts'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Debts') }} /</span> {{ __('Debts') }}
</h4>
<!-- Basic Bootstrap Table -->
<div class="card p-2">
  <h5 class="card-header">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDebt">
      {{ __('Add Debt') }}
    </button>
  </h5>
  @include('content.Debt.create')
  <div class="table-responsive text-nowrap">
    <table id="datatable-debt" class="table table-hover is-stripedt">
      <thead>
          <tr>
              <th >#</th>
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
            <td>{{ $debt->fullname }}</td>
            <td>{{ $debt->phone }}</td>
            <td>
              <table class="table table-sm table-bordered">
                <tbody>
                  @foreach ($debt->getDebtProduct  as $item)
                    <tr  >
                      <td>{{ $item->name }}</td>
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
              <a href="{{ route('debt.show', $debt->id) }}">
                <span class="badge bg-label-info"><i class='bx bx-show me-1'></i></span>
              </a>
              <a href="{{ route('debt.edit', $debt->id) }}">
                <span class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteDebt{{ $debt->id }}">
                <span class="badge bg-label-danger"><i class="bx bx-trash me-1"></i></span>
              </a>
            </td>
          </tr>
          @include('content.Debt.delete')
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  new DataTable('#datatable-debt', {
    initComplete: function () {
        this.api()
            .columns()
            .every(function () {
                let column = this;
                let title = column.footer().textContent;

                // Create input element
                let input = document.createElement('input');
                input.placeholder = title;
                column.footer().replaceChildren(input);

                // Event listener for user input
                input.addEventListener('keyup', () => {
                    if (column.search() !== this.value) {
                        column.search(input.value).draw();
                    }
                });
            });
    }
  });

</script>
{{-- ! js for model created (create.blade.php) in order to add inputs --}}
<script>
  $(document).ready(function() {
      // Function to add new product row
      $('#add-product-create').click(function() {
          let productRowCreate = `
          <div class="row g-1 product-row-create">
              <div class="col-md-3 mb-3">
                  <label for="name-product" class="form-label">{{ __('Name Product') }}</label>
                  <select id="name-product" class="form-select" name="name_product[]" required>
                      <option value="">{{ __('Choose a product') }}</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-3 mb-3">
                  <label for="quantity" class="form-label">{{ __('Quantity') }}</label>
                  <input type="number" id="quantity" name="quantity[]" class="form-control" min="0" placeholder="{{ __('Enter Quantity') }}" required>
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
  });
</script>
{{-- ! js for model modify (edit.blade.php) in order to add inputs --}}
<script>
  $(document).ready(function() {
      // Function to add new product row
      $(document).on('click', '[id^=add-product-edit-]', function() {
          let debtId = $(this).attr('id').split('-').pop(); // Extract the debt id from the button's ID
          let productRowEdit = `
          <div class="row g-1 product-row-edit">
            <input type="hidden" name="id[]" value="-1">
              <div class="col-md-3 mb-3">
                  <label class="form-label">{{ __('Name Product') }}</label>
                  <select class="form-select" name="name_product[]" required>
                      <option value="">{{ __('Choose a product') }}</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-3 mb-3">
                  <label class="form-label">{{ __('Quantity') }}</label>
                  <input type="number" name="quantity[]" class="form-control" min="0" placeholder="{{ __('Enter Quantity') }}" required>
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
  });
</script>
@endsection

