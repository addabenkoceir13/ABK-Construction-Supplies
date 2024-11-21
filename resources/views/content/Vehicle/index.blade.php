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
              <th >{{ __('Type vehicle') }}</th>
              <th >{{ __('License plate') }}</th>
              <th >{{ __('Action') }}</th>
          </tr>

      </thead>
      <tbody>
        @foreach ($vehicles as $vehicle)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $vehicle->name }}</td>
            <td class="text-center">
              @switch($vehicle->type)
                @case('car')
                  <span class="badge bg-label-danger"><i class='bx bx-car'></i></span>
                  @break
                @case('truck')
                  <span class="badge bg-label-warning"><i class='bx bxs-truck' ></i></span>
                  @break
                @case('motorcycle')
                  <span class="badge bg-label-facebook"><i class='bx bx-cycling'></i></span>
                  @break
              @endswitch
            </td>
            <td>{{ $vehicle->license_plate }}</td>
            <td>
              <a href="javascript:void(0);" class="pay-btn" data-bs-toggle="modal" data-bs-target="#modalEditVehicle{{ $vehicle->id }}">
                <span class="badge bg-label-success" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-edit bx-xs' ></i> <span>{{ __('Modify Vehicle') }}</span>">
                <i class='bx bx-edit bx-xs'></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteVehicle{{ $vehicle->id }}">
                <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete  Vehicle') }}</span>">
                <i class="bx bx-trash me-1"></i></span>
              </a>
              @if ($vehicle->insuranceDateExpiredLast())
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAddDateInsuranceVehicle-{{ $vehicle->id }}">
                  <span class="badge bg-label-hover-linkedin" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Added date insurance vehicle') }}</span>">
                  <i class='bx bx-calendar-plus me-1'></i>
                </a>
              @endif
          </tr>
          @include('content.Vehicle.edit')
          @include('content.Vehicle.delete')
          @include('content.Vehicle.added-date')
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




