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
          {{ $fuelStation->vehicle->name }} |
          {{ $fuelStation->vehicle->license_plate }}
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


