@extends('layouts/contentNavbarLayout')

@section('title', __('Fuel Purchase Accounting'))

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Fuel Purchase Accounting') }} /</span> {{ __('Fuel Purchase Accounting') }}
</h4>
<!-- Basic Bootstrap Table -->
<div class="card p-2">
  <form id="filter-form" method="GET" action="{{ route('fuel-stations.index') }}">
    <div class="row">
      <div class="col-md-4">
        <div class="row">
          <label class="col-sm-2 col-form-label" for="start_date">{{ __('From') }}</label>
          <div class="col-sm-10">
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}"  />
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="row">
          <label class="col-sm-2 col-form-label" for="end_date">{{ __('To') }}</label>
          <div class="col-sm-10">
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}"  />
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <button type="button" id="clear" class="btn btn-outline-pinterest btn-sm mt-1">{{ __('Clean') }}</button>
      </div>
    </div>
  </form>
  @php
    $total = 0;
    foreach ($fuelStations as $fuelStation)
    {
      $total = $total + $fuelStation->amount;
    }
    
  @endphp
  <h5 class="card-header d-flex justify-content-between">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddFuelReceipt">
      {{ __('Add Fuel Receipt') }}
    </button>
    <div class="d-flex">
      <p class="mx-2">{{ __('Total Amount: ') }}</p>
      <p>{{ number_format($total , 2)  }} {{ __('DZ') }}</p>
    </div>
  </h5>
  @include('content.Fuelstation.add')
  <div class="table-responsive text-nowrap">
    <table id="datatable-fuelstation" class="table table-hover is-stripedt">
      <thead>
          <tr>
              <th >#</th>
              <th >{{ __('Vehicle') }}</th>
              <th >{{ __('Name Owner') }}</th>
              <th >{{ __('Name Driver') }}</th>
              <th >{{ __('Name Distributor') }}</th>
              <th >{{ __('Filing Datetime') }}</th>
              <th >{{ __('Liter') }}</th>
              <th >{{ __('Amount') }}</th>
              <th >{{ __('Create At') }}</th>
              <th >{{ __('Status') }}</th>
              <th >{{ __('Action') }}</th>
          </tr>
      </thead>
      <tbody>
        @foreach ($fuelStations as $fuelStation)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              @switch($fuelStation->vehicle->type)
                @case('car')
                  <span class="badge bg-label-danger me-1"><i class='bx bx-car'></i></span>
                  @break
                @case('truck')
                  <span class="badge bg-label-warning me-1"><i class='bx bxs-truck' ></i></span>
                  @break
                @case('motorcycle')
                  <span class="badge bg-label-facebook me-1"><i class='bx bx-cycling'></i></span>
                  @break
              @endswitch
              {{ $fuelStation->vehicle->name }}
            </td>
            <td>{{ $fuelStation->name_owner }}</td>
            <td>{{ $fuelStation->name_driver }}</td>
            <td>{{ $fuelStation->name_distributor }}</td>
            <td>{{ $fuelStation->filing_datetime }}</td>
            <td>{{ $fuelStation->liter }}</td>
            <td>{{ $fuelStation->amount }}</td>
            <td>{{ $fuelStation->created_at->format('Y-m-d') }}</td>
            <td>
              @if ($fuelStation->status == 'paid')
                <span class="badge bg-label-success me-1">{{ __('Paid') }}</span>
              @else
                <span class="badge bg-label-warning me-1">{{ __('Unpaid') }}</span>
              @endif
            </td>
            <td>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditFuelStation-{{ $fuelStation->id }}">
                <span class="badge bg-label-success" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Edit Fuel Receipt') }}</span>">
                <i class="bx bx-edit-alt me-1"></i></span>
              </a>
              @include('content.Fuelstation.edit')
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteFuelStation-{{ $fuelStation->id }}">
                <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete Fuel Receipt') }}</span>">
                <i class="bx bx-trash me-1"></i></span>
              </a>
              @include('content.Fuelstation.delete')
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalPaidedFuelStation-{{ $fuelStation->id }}" data-row-id="{{ $fuelStation->id }}">
                <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Pay a debt') }}</span>">
                <i class='bx bx-money'></i></span>
              </a>
              @include('content.Fuelstation.paided')
            </td>
          </tr>
          {{-- @include('content.DebtWithSupplier.payDebt') --}}
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
      new DataTable('#datatable-fuelstation', {
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

      $(document).on('click', '#clear', function () {
        $('#start_date').val('');
        $('#end_date').val('');

        // Update the URL to remove date parameters
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');

        // Push the updated URL to the browser history
        history.pushState(null, '', url);

        // Optionally submit the form if required to refresh results
        $('#filter-form').submit();
      })
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const filterForm = document.getElementById('filter-form');
      const startDate = document.getElementById('start_date');
      const endDate = document.getElementById('end_date');

      startDate.addEventListener('change', function () {
          filterForm.submit();
      });

      endDate.addEventListener('change', function () {
          filterForm.submit();
      });
  });

</script>


@endsection

