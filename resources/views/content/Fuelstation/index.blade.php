@extends('layouts/contentNavbarLayout')

@section('title', __('Fuel Accounting'))

@section('content')

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Fuel Accounting') }} /</span> {{ __('Fuel Accounting Paid') }}
</h4>
<!-- Basic Bootstrap Table -->
<div class="card p-2">
  <h5 class="card-header">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddVehicle">
      {{ __('Add Fuel Accounting') }}
    </button>
  </h5>
  @include('content.Vehicle.create')
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
              <th >{{ __('Supplier') }}</th>
              <th >{{ __('Vehicle') }}</th>
              <th >{{ __('Name Ower') }}</th>
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
              <a href="{{ route('debt-supplier.show', $fuelStation->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-show me-1'></i> <span>{{ __('View Debt') }}</span>">
                <span class="badge bg-label-info"><i class='bx bx-show me-1'></i></span>
              </a>
              <a href="{{ route('debt-supplier.edit', $fuelStation->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-edit bx-xs' ></i> <span>{{ __('Modify Debt') }}</span>">
                <span class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteDebt{{ $fuelStation->id }}">
                <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete  debt') }}</span>">
                <i class="bx bx-trash me-1"></i></span>
              </a>
              <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#PayDebtModal{{ $fuelStation->id }}" data-row-id="{{ $fuelStation->id }}">
                <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Pay a debt') }}</span>">
                <i class='bx bx-money'></i></span>
              </a>

            </td>
          </tr>
          {{-- @include('content.DebtWithSupplier.delete') --}}
          {{-- @include('content.DebtWithSupplier.payDebt') --}}
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




@endsection

