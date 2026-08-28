@extends('layouts/contentNavbarLayout')

@section('title', __('Debts'))

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Debts') }} /</span> {{ __('Debts') }}
</h4>

<x-debt-search-card
  :action="route('debt.index-paid')"
  :name="request('name')"
  :phone="request('phone')"
  :result-count="$debts->total()"
/>

<!-- Basic Bootstrap Table -->
<div class="card p-2">
  {{-- <h5 class="card-header">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDebt">
      {{ __('Add Debt') }}
    </button>
  </h5>
  @include('content.Debt.create') --}}

  <div id="debts-table-region">
    @include('content.Debt._debtsTable')
  </div>
</div>
<!--/ Basic Bootstrap Table -->
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
<script>
$(document).ready(function() {
    new DataTable('#datatable-debt', {
      paging: false, // server-side pagination now bounds the rows in the DOM; see the links() control below the table
      searching: false, // client-side search replaced by the search card above the table
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
<script>
  $(document).ready(function() {
      $('#datatable-debt').DataTable(); // Initialize the DataTable

      // Apply custom styles with JavaScript
      $('.dataTables_filter input').css({
          'border': '1px solid #ccc',
          'border-radius': '4px',
          'padding': '6px',
          'width': '200px'
      });

      $('.dataTables_length select').css({
          'border': '1px solid #ccc',
          'border-radius': '4px',
          'padding': '6px',
          'width': 'auto'
      });

      // Optional: Add hover and focus effects for the search input
      $('.dataTables_filter input').hover(
          function() { $(this).css('border-color', '#007bff'); },
          function() { $(this).css('border-color', '#ccc'); }
      ).focus(function() {
          $(this).css({
              'border-color': '#0056b3',
              'box-shadow': '0 0 5px rgba(0, 123, 255, 0.5)'
          });
      }).blur(function() {
          $(this).css({
              'border-color': '#ccc',
              'box-shadow': 'none'
          });
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
                      <input type="number" class="form-control" name="amount_due[]" placeholder="100" min="0" aria-label="Amount (to the nearest DZ)" required>
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

      $('#fullname-search').on('keyup', function()
      {
          var query = $(this).val();
          $.ajax({
              url:'{{ route('debt.search') }}',
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              method: 'POST',
              data: {query: query},
              success: function(data){
                  if (data.status) {
                      array = data.query;
                      let suggestions = data.query;
                      let dataList = $('#listFullName');
                      dataList.empty(); // Clear previous options
                      suggestions.forEach((item) => {
                          let option = $('<option>').val(item.name);
                          dataList.append(option);
                      });

                  }
              }
          })
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

