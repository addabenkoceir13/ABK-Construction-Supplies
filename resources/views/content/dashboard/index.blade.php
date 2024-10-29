@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard - Analytics'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <div class="row">
    <div class="col-lg-4 col-md-12 col-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <img src="{{asset('assets/img/icons/unicons/cc-primary.png')}}" alt="chart primary" class="rounded">
            </div>
          </div>
          <span class="fw-semibold d-block mb-1">{{ __('Total debts') }}</span>
          <h3 class="card-title mb-2">{{ number_format($TotalDebt, 2) }}</h3>
          <small class="text-primary fw-semibold"><i class='bx bx-up-arrow-alt'></i> +0%</small>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-12 col-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <img src="{{asset('assets/img/icons/unicons/cc-warning.png')}}" alt="chart warning" class="rounded">
            </div>
          </div>
          <span class="fw-semibold d-block mb-1">{{ __('Total outstanding not debts') }}</span>
          <h3 class="card-title mb-2">{{ number_format($TotalRestDebt, 2) }}</h3>
          <small class="text-warning fw-semibold"><i class='bx bx-up-arrow-alt'></i> +0%</small>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-12 col-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <img src="{{asset('assets/img/icons/unicons/cc-success.png')}}" alt="chart success" class="rounded">
            </div>
          </div>
          <span class="fw-semibold d-block mb-1">{{ __('Total debt paid') }}</span>
          <h3 class="card-title mb-2">{{ number_format($TotalPaidDebt, 2) }}</h3>
          <small class="text-success fw-semibold"><i class='bx bx-up-arrow-alt'></i> +0%</small>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
