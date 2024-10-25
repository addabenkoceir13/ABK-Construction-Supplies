@extends('layouts/contentNavbarLayout')

@section('title', __('Debts'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light"><a href="{{ route('debt.index') }}">{{ __('Debts') }}</a> / </span>
  {{ __('View Debt') }}
</h4>

<div class="col-12">
  <div class="card mb-4">
    <h5 class="card-header">{{ __('View Debt') }}</h5>
    <div class="card-body">
      <h5 class="mb-3"><i class='bx bx-info-circle me-1' ></i>{{ __('information Client') }}</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class="bx bx-user mx-1"></i>{{ __('Customer Name') }} : </small>
              <span class="me-2">{{ $debt->fullname }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class="bx bx-phone mx-1"></i>{{ __('Customer Phone') }} : </small>
              <span>{{ $debt->phone }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date first Debt') }} : </small>
              <span>{{ $debt->date_debut_debt }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Total debt') }} : </small>
              <span>{{ $debt->total_debt_amount }} {{ __('DZ') }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date last debt') }} : </small>
              <span>{{ is_null($debt->date_end_debt) ? '   /   ' : $debt->date_end_debt }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Remaining debt') }} : </small>
              <span>{{ is_null($debt->rest_debt_amount) ? 0.00 : $debt->rest_debt_amount }} {{ __('DZ') }}</span>
            </h5>
          </div>

          <div class="col-md-6 mb-3">
            <h5>
              @if ($debt->status == 'unpaid')
                <small class="text-light fw-semibold"><i class='bx bx-checkbox mx-1'></i>{{ __('Status') }} : </small>
                <span class="badge bg-label-warning">{{ __('Unpaid') }}</span>
              @else
                <small class="text-light fw-semibold"><i class='bx bx-checkbox-checked mx-1' ></i>{{ __('Status') }} : </small>
                <span class="badge bg-label-success">{{ __('Paid') }}</span>
              @endif
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-message-alt mx-1'></i>{{ __('Note') }} : </small>
              <span>{{ is_null($debt->note) ? '/' : $debt->note }} </span>
            </h5>
          </div>

          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date created') }} : </small>
              <span>{{ $debt->created_at }}</span>
            </h5>
          </div>
          <div class="col-md-6 mb-3">
            <h5>
              <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date updated') }} : </small>
              <span>{{  $debt->updated_at }} </span>
            </h5>
          </div>

        </div>
    </div>
  </div>

  <div class="divider divider-primary">
    <div class="divider-text"><i class='bx bx-cube-alt'></i></div>
  </div>

  <div class="card mb-3">
    <h5 class="card-header mb-4"><i class='bx bx-info-circle me-1' ></i>{{ __('View Debt') }}</h5>
    <div class="card-body">
      <div class="row g-1 product-row-edit">
          @foreach ($debt->getDebtProduct  as $item)
            <div class="col-md-3 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bxs-checkbox mx-1'></i>{{ __('Name Product') }} : </small>
                <span>{{ $item->name_category }}</span>
              </h5>
            </div>
            <div class="col-md-3 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bxs-checkbox mx-1'></i>{{ __('Quantity') }} : </small>
                <span>{{  $item->quantity }}</span>
              </h5>
            </div>
            <div class="col-md-3 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Amount Due') }} : </small>
                <span>{{ $item->amount }} {{ __('DZ') }}</span>
              </h5>
            </div>
            <div class="col-md-3 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date Debut Debt') }} : </small>
                <span>{{ $item->date_debt }}</span>
              </h5>
            </div>
          @endforeach
      </div>
    </div>
  </div>
</div>
@endsection


