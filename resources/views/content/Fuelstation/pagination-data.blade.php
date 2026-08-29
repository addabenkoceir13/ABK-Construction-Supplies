<table id="datatable-fuelstation" class="table table-hover align-middle mb-0">
  <thead class="table-light">
    <tr>
      <th class="text-center" style="width: 40px;">#</th>
      <th class="text-center" style="width: 40px;">
        <input class="form-check-input" type="checkbox" id="select-all-page">
      </th>
      <th><i class="bx bx-car me-1 text-primary"></i>{{ __('Vehicle') }}</th>
      <th><i class="bx bx-store me-1 text-primary"></i>{{ __('Station') }}</th>
      <th><i class="bx bx-user me-1 text-primary"></i>{{ __('Driver') }}</th>
      <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Filing Date') }}</th>
      <th class="text-center"><i class="bx bx-water me-1 text-primary"></i>{{ __('Liters') }}</th>
      <th class="text-end"><i class="bx bx-money me-1 text-primary"></i>{{ __('Amount') }}</th>
      <th class="text-center"><i class="bx bx-check-shield me-1 text-primary"></i>{{ __('Status') }}</th>
      <th class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($fuelStations as $fuelStation)
      <tr>
        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
        <td class="text-center">
          <input type="checkbox" class="form-check-input row-checkbox" name="ids[]" value="{{ $fuelStation->id }}">
        </td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
              @switch($fuelStation->vehicle->type ?? 'car')
                @case('car')
                  <i class='bx bx-car'></i>
                  @break
                @case('truck')
                  <i class='bx bxs-truck'></i>
                  @break
                @case('motorcycle')
                  <i class='bx bx-cycling'></i>
                  @break
                @default
                  <i class='bx bx-car'></i>
              @endswitch
            </div>
            <div>
              <span class="fw-semibold text-heading d-block">{{ $fuelStation->vehicle->name ?? '-' }}</span>
              <span class="badge bg-light text-muted border font-monospace small px-2 py-0">
                {{ $fuelStation->vehicle->license_plate ?? '-' }}
              </span>
            </div>
          </div>
        </td>
        <td>
          <span class="text-heading small fw-semibold">{{ $fuelStation->name_owner ?? '-' }}</span>
          @if($fuelStation->name_distributor)
            <small class="text-muted d-block">{{ $fuelStation->name_distributor }}</small>
          @endif
        </td>
        <td>
          <span class="text-heading small">{{ $fuelStation->name_driver ?? '-' }}</span>
        </td>
        <td>
          <div class="text-muted small">
            <i class="bx bx-calendar me-1 text-primary"></i>
            <span>{{ $fuelStation->filing_datetime ? date('Y-m-d H:i', strtotime($fuelStation->filing_datetime)) : $fuelStation->created_at->format('Y-m-d') }}</span>
          </div>
        </td>
        <td class="text-center">
          <span class="badge bg-label-info rounded-pill px-3 py-2 fw-semibold">
            {{ number_format($fuelStation->liter, 2) }} L
          </span>
        </td>
        <td class="text-end">
          <span class="fw-bold text-primary fs-6">
            {{ number_format($fuelStation->amount, 2) }} <small class="text-muted">DZ</small>
          </span>
        </td>
        <td class="text-center">
          @if ($fuelStation->status == 'paid')
            <span class="badge bg-label-success rounded-pill px-3 py-2">
              <i class="bx bx-check-circle me-1"></i>{{ __('Paid') }}
            </span>
          @else
            <span class="badge bg-label-warning rounded-pill px-3 py-2">
              <i class="bx bx-time-five me-1"></i>{{ __('Unpaid') }}
            </span>
          @endif
        </td>
        <td class="text-center">
          <div class="d-flex align-items-center justify-content-center gap-1">
            <button type="button" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditFuelStation-{{ $fuelStation->id }}" title="{{ __('Edit Fuel Receipt') }}">
              <i class="bx bx-edit-alt"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPaidedFuelStation-{{ $fuelStation->id }}" title="{{ __('Pay a debt') }}">
              <i class="bx bx-money"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteFuelStation-{{ $fuelStation->id }}" title="{{ __('Delete Fuel Receipt') }}">
              <i class="bx bx-trash"></i>
            </button>
          </div>
          @include('content.Fuelstation.edit')
          @include('content.Fuelstation.delete')
          @include('content.Fuelstation.paided')
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="10" class="text-center py-5 text-muted">
          <i class="bx bx-gas-pump fs-2 d-block mb-1"></i>
          {{ __('No fuel purchase receipts found') }}
        </td>
      </tr>
    @endforelse
  </tbody>
</table>
