@extends('layouts/contentNavbarLayout')

@section('title', __('View Supplier Debt') . ' - ' . $debt->fullname)

@section('content')
@php
    $totalDebt = (float)$debt->total_debt_amount;
    $restDebt = (float)($debt->rest_debt_amount ?? 0);
    $paidAmount = max(0, $totalDebt - $restDebt);
    $progressPercent = $totalDebt > 0 ? min(100, round(($paidAmount / $totalDebt) * 100)) : 0;
@endphp

<!-- Page Header & Action Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light"><a href="{{ route('debt-supplier.index') }}" class="text-muted text-decoration-none">{{ __('Supplier Debts') }}</a> /</span> {{ __('Supplier Debt Dossier') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Complete statement of supplier delivery debt, materials breakdown, and payment progress.') }}</p>
  </div>
  <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
    <a href="{{ route('debt-supplier.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
      <i class="bx bx-arrow-back"></i>
      <span>{{ __('Back') }}</span>
    </a>
    <a href="{{ route('debt-supplier.edit', $debt->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
      <i class="bx bx-edit-alt"></i>
      <span>{{ __('Edit Debt') }}</span>
    </a>
    <a href="{{ route('debt.printer-facteur-client', ['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" target="_blank" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
      <i class="bx bx-printer"></i>
      <span>{{ __('Print Invoice') }}</span>
    </a>
    <a href="{{ route('debt.download-facteur-client', ['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" class="btn btn-outline-danger d-flex align-items-center gap-1 shadow-sm" title="{{ __('Download PDF Invoice') }}">
      <i class="bx bxs-file-pdf"></i>
      <span>{{ __('Download PDF') }}</span>
    </a>
  </div>
</div>

<!-- Financial KPI Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Total Debt') }}</span>
          <h4 class="card-title mb-0 fw-bold text-primary">{{ number_format($totalDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
        </div>
        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-wallet fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Amount Paid') }}</span>
          <h4 class="card-title mb-0 fw-bold text-success">{{ number_format($paidAmount, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
        </div>
        <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-check-circle fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Remaining Balance') }}</span>
          <h4 class="card-title mb-0 fw-bold text-danger">{{ number_format($restDebt, 2) }} <small class="fs-6 text-muted">DZ</small></h4>
        </div>
        <div class="avatar avatar-md bg-label-danger rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-time-five fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3 col-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="text-muted small fw-semibold">{{ __('Settlement Rate') }}</span>
          <span class="fw-bold text-heading small">{{ $progressPercent }}%</span>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="mt-2 text-center">
          @if ($debt->status == 'paid')
            <span class="badge bg-label-success rounded-pill px-3 py-1">
              <i class="bx bx-check-circle me-1"></i>{{ __('Fully Paid') }}
            </span>
          @else
            <span class="badge bg-label-warning rounded-pill px-3 py-1">
              <i class="bx bx-time-five me-1"></i>{{ __('Outstanding Balance') }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Supplier & Client Information Card -->
  <div class="col-lg-4 col-12">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
          <i class="bx bx-user-pin fs-6"></i>
        </div>
        <h6 class="card-title mb-0 fw-semibold">{{ __('Supplier & Client Details') }}</h6>
      </div>
      <div class="card-body pt-4">
        <!-- Dedicated Supplier Badge Box -->
        <div class="alert alert-primary bg-label-primary border-0 rounded-3 p-3 mb-4">
          <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bx bxs-truck fs-5 text-primary"></i>
            <span class="small text-muted fw-semibold">{{ __('Tractor Driver / Supplier') }}</span>
          </div>
          <h5 class="fw-bold text-primary mb-0">{{ $debt->tractorDriver->fullname ?? '-' }}</h5>
          @if($debt->tractorDriver && $debt->tractorDriver->phone)
            <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $debt->tractorDriver->phone }}</small>
          @endif
        </div>

        <div class="text-center mb-4">
          <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold fs-3">
            {{ strtoupper(mb_substr($debt->fullname, 0, 1)) }}
          </div>
          <h5 class="fw-bold mb-0 text-heading">{{ $debt->fullname }}</h5>
          <p class="text-muted small mb-0">{{ __('Customer / Beneficiary') }}</p>
        </div>

        <ul class="list-group list-group-flush border rounded-3 p-2">
          <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 border-bottom">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-phone text-primary"></i> {{ __('Phone') }}
            </span>
            @if($debt->phone)
              <a href="tel:{{ $debt->phone }}" class="fw-semibold text-primary text-decoration-none">{{ $debt->phone }}</a>
            @else
              <span class="text-muted">-</span>
            @endif
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 border-bottom">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-calendar text-primary"></i> {{ __('First Debt Date') }}
            </span>
            <span class="fw-semibold text-heading">{{ $debt->date_debut_debt }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 border-bottom">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-calendar-check text-primary"></i> {{ __('Last Debt Date') }}
            </span>
            <span class="fw-semibold text-heading">{{ $debt->date_end_debt ?? '-' }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 border-bottom">
            <span class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-time text-primary"></i> {{ __('Created At') }}
            </span>
            <span class="text-muted small">{{ $debt->created_at->format('Y-m-d H:i') }}</span>
          </li>
          <li class="list-group-item px-2 py-2 border-0">
            <span class="text-muted small d-block mb-1">
              <i class="bx bx-message-square-detail text-primary"></i> {{ __('Notes / Remarks') }}
            </span>
            <p class="mb-0 text-heading small bg-light p-2 rounded">{{ $debt->note ?: __('No additional notes provided.') }}</p>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Products Breakdown Table Card -->
  <div class="col-lg-8 col-12">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
            <i class="bx bx-cube-alt fs-6"></i>
          </div>
          <h6 class="card-title mb-0 fw-semibold">{{ __('Delivered Materials & Products') }}</h6>
        </div>
        <span class="badge bg-label-primary rounded-pill">{{ count($debt->getDebtProduct) }} {{ __('Items') }}</span>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th><i class="bx bx-cube me-1 text-primary"></i>{{ __('Material / Product') }}</th>
              <th class="text-center"><i class="bx bx-tag me-1 text-primary"></i>{{ __('Quantity / Unit') }}</th>
              <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Date') }}</th>
              <th class="text-end"><i class="bx bx-money me-1 text-primary"></i>{{ __('Amount') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($debt->getDebtProduct as $item)
              <tr>
                <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                      <i class="bx bx-package"></i>
                    </div>
                    <span class="fw-semibold text-heading">{{ $item->name_category }}</span>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-label-info rounded-pill px-3 py-2 fw-semibold">
                    {{ $item->quantity }} {{ $item->getSubcategory->display_name ?? '' }}
                  </span>
                </td>
                <td>
                  <div class="text-muted small">
                    <i class="bx bx-calendar me-1 text-primary"></i>
                    <span>{{ $item->date_debt }}</span>
                  </div>
                </td>
                <td class="text-end">
                  <span class="fw-bold text-primary fs-6">{{ number_format($item->amount, 2) }} DZ</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
                  {{ __('No products listed for this supplier debt') }}
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot class="table-light border-top">
            <tr>
              <th colspan="4" class="text-end fw-bold text-heading">{{ __('Total Debt Amount:') }}</th>
              <th class="text-end fw-bold text-primary fs-5">{{ number_format($totalDebt, 2) }} DZ</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection


