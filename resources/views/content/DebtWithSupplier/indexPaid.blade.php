@extends('layouts/contentNavbarLayout')

@section('title', __('Debts Supplier Paid'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Debts') }} /</span> {{ __('Debts Supplier Paid Archive') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Archive of all settled supplier and tractor driver debts.') }}</p>
  </div>
</div>

{{-- 1. Dedicated Search & Filter Card --}}
<x-debt-search-card
  :action="route('debt-supplier.index-paid')"
  :name="request('name')"
  :phone="request('phone')"
  :result-count="$debts->total()"
/>

{{-- 2. Dedicated Table Card --}}
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-check-circle fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Paid Supplier Debts') }}</h5>
        <small class="text-muted">{{ __('Total settled records:') }} <strong class="text-success">{{ $debts->total() }}</strong></small>
      </div>
    </div>
  </div>

  <div id="debts-table-region">
    @include('content.DebtWithSupplier._debtsTable', ['tractorDriverLabel' => __('Supplier')])
  </div>
</div>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

