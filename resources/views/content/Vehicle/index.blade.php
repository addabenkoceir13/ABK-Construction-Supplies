@extends('layouts/contentNavbarLayout')

@section('title', __('Vehicle'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Vehicle') }} /</span> {{ __('Vehicle') }}
</h4>
<!-- Basic Bootstrap Table -->
<div class="card p-2">
  <h5 class="card-header">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddVehicle">
      {{ __('Add Vehicle') }}
    </button>
  </h5>
  @include('content.Vehicle.create')

  <div class="table-responsive text-nowrap">
    <table id="datatable-debt" class="table table-hover is-stripedt">
      <thead>
          <tr>
              <th >#</th>
              <th >{{ __('Name') }}</th>
              <th >{{ __('Phone') }}</th>
              <th >{{ __('Create At') }}</th>
              <th >{{ __('Status') }}</th>
              <th >{{ __('Action') }}</th>
          </tr>

      </thead>
      <tbody>
        @foreach ($suppliers as $supplier)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $supplier->fullname }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->created_at->format('Y-m-d') }}</td>
            <td>
              @if ($supplier->status == 'active')
                <span class="badge bg-label-success me-1">{{ __('Active') }}</span>
              @else
                <span class="badge bg-label-warning me-1">{{ __('Inactive') }}</span>
              @endif
            </td>
            <td>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalditVehicle{{ $supplier->id }}">
                <span data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-edit bx-xs' ></i> <span>{{ __('Modify Vehicle') }}</span>" class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
              </a>
              @include('content.Vehicle.edit')
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteVehicle{{ $supplier->id }}">
                <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete Vehicle') }}</span>">
                <i class="bx bx-trash me-1"></i></span>
              </a>
              @include('content.Vehicle.deleted')
            </td>
          </tr>
        @endforeach
      </tbody>
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
@endsection




