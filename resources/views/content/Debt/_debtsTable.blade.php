<div class="table-responsive text-nowrap" data-search-total="{{ $debts->total() }}">
  <table id="datatable-debt" class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th class="text-center" style="width: 50px;">#</th>
        <th><i class="bx bx-user me-1 text-primary"></i>{{ __('Customer') }}</th>
        <th><i class="bx bx-package me-1 text-primary"></i>{{ __('Debts & Products') }}</th>
        <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Debt Date') }}</th>
        <th class="text-center"><i class="bx bx-check-shield me-1 text-primary"></i>{{ __('Status') }}</th>
        <th class="text-center" style="width: 160px;">{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody>

      @forelse ($debts as $debt)
        <tr data-row-id="{{ $debt->id }}">
          <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                {{ strtoupper(mb_substr($debt->fullname, 0, 1)) }}
              </div>
              <div>
                <span class="fw-semibold text-heading d-block">{{ $debt->fullname }}</span>
                @if($debt->phone)
                  <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bx bx-phone fs-7"></i>
                    <a href="tel:{{ $debt->phone }}" class="text-muted text-decoration-none">{{ $debt->phone }}</a>
                  </small>
                @endif
              </div>
            </div>
          </td>
          <td>
            <div class="debt-products-list d-flex flex-column gap-1" style="min-width: 240px; max-width: 320px;">
              @foreach ($debt->getDebtProduct as $item)
                <div class="d-flex align-items-center justify-content-between bg-lighter rounded px-2 py-1 small">
                  <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-label-secondary rounded-pill py-1">{{ $item->name_category }}</span>
                    <span class="text-muted small">× {{ $item->quantity }}</span>
                  </div>
                  <span class="fw-semibold text-dark ms-2">{{ number_format($item->amount, 2) }} {{ __('DZ') }}</span>
                </div>
              @endforeach
              <div class="d-flex justify-content-between align-items-center pt-1 border-top mt-1 px-1">
                <span class="small fw-bold text-muted">{{ __('Total') }}:</span>
                <span class="badge bg-label-primary fs-7 fw-bold">{{ number_format($debt->total_debt_amount, 2) }} {{ __('DZ') }}</span>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex align-items-center text-muted">
              <i class="bx bx-calendar me-1 text-primary fs-6"></i>
              <span class="fw-medium">{{ $debt->date_debut_debt }}</span>
            </div>
          </td>
          <td class="text-center">
            @if ($debt->status == 'paid')
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
              <a href="{{ route('debt.show', $debt->id) }}" class="btn btn-icon btn-sm btn-outline-info" data-bs-toggle="tooltip" title="{{ __('View Debt') }}">
                <i class="bx bx-show"></i>
              </a>
              <a href="{{ route('debt.edit', $debt->id) }}" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="tooltip" title="{{ __('Modify Debt') }}">
                <i class="bx bx-edit-alt"></i>
              </a>
              <button type="button" class="btn btn-icon btn-sm btn-outline-primary pay-btn" data-bs-toggle="modal" data-bs-target="#PayDebtModal{{ $debt->id }}" data-row-id="{{ $debt->id }}" title="{{ __('Pay a debt') }}">
                <i class="bx bx-money"></i>
              </button>
              <a href="{{ route('debt.printer-facteur-client',['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ __('Print Invoice') }}">
                <i class="bx bx-printer"></i>
              </a>
              <button type="button" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteDebt{{ $debt->id }}" title="{{ __('Delete debt') }}">
                <i class="bx bx-trash"></i>
              </button>
            </div>
            @include('content.Debt.delete')
            @include('content.Debt.payDebt')
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center py-5">
            <div class="d-flex flex-column align-items-center justify-content-center">
              <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <i class="bx bx-folder-open fs-2 text-muted"></i>
              </div>
              <h6 class="text-muted mb-1">{{ __('No debts found') }}</h6>
              <p class="text-muted small mb-0">{{ __('Try adjusting your search criteria or add a new debt.') }}</p>
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center py-3 border-top gap-2">
    <div class="small text-muted">
      @if ($debts->total() > 0)
        {{ __('Showing') }} <span class="fw-semibold">{{ $debts->firstItem() }}</span> {{ __('to') }} <span class="fw-semibold">{{ $debts->lastItem() }}</span> {{ __('of') }} <span class="fw-semibold">{{ $debts->total() }}</span> {{ __('entries') }}
      @else
        {{ __('No entries available') }}
      @endif
    </div>
    <div class="pagination-wrapper mb-0">
      {{ $debts->withQueryString()->links() }}
    </div>
  </div>
</div>
