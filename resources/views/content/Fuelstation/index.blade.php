@extends('layouts/contentNavbarLayout')

@section('title', __('Fuel Purchase Accounting'))

@section('content')
@php
    $total = 0;
    foreach ($fuelStations as $fuelStation) {
        $total = $total + $fuelStation->amount;
    }
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Accounting') }} /</span> {{ __('Fuel Purchase Accounting') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Track fleet fuel receipts, diesel/gasoline refills, station vouchers, and settlement records.') }}</p>
  </div>
  <div class="d-flex gap-2 mt-2 mt-sm-0 align-items-center">
    <div class="bg-white shadow-sm border rounded-pill px-3 py-2 d-flex align-items-center gap-2">
      <span class="text-muted small fw-semibold">{{ __('Total Fuel Amount:') }}</span>
      <span class="fw-bold text-primary total-amount fs-6">{{ number_format($total, 2) }} {{ __('DZ') }}</span>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddFuelReceipt">
      <i class="bx bx-plus-circle"></i>
      <span>{{ __('Add Fuel Receipt') }}</span>
    </button>
  </div>
</div>

<!-- 1. Filter Card -->
<div class="card mb-4 shadow-sm border-0">
  <div class="card-header bg-transparent pb-1 pt-3 d-flex align-items-center gap-2">
    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
      <i class="bx bx-filter-alt fs-6"></i>
    </div>
    <h6 class="card-title mb-0 fw-semibold">{{ __('Filter Fuel Purchases') }}</h6>
  </div>
  <div class="card-body pt-2">
    <form id="filter-form" method="GET" action="{{ route('fuel-stations.index') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label for="search" class="form-label text-muted small fw-semibold mb-1">{{ __('Search') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-search text-muted"></i></span>
            <input type="text" class="form-control" id="search" placeholder="{{ __('Search vehicle, driver, station...') }}">
          </div>
        </div>
        <div class="col-md-3">
          <label for="start_date" class="form-label text-muted small fw-semibold mb-1">{{ __('From Date') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-calendar text-muted"></i></span>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}" />
          </div>
        </div>
        <div class="col-md-3">
          <label for="end_date" class="form-label text-muted small fw-semibold mb-1">{{ __('To Date') }}</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text bg-lighter"><i class="bx bx-calendar text-muted"></i></span>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}" />
          </div>
        </div>
        <div class="col-md-2">
          <label for="entries" class="form-label text-muted small fw-semibold mb-1">{{ __('Show Entries') }}</label>
          <select class="form-select" id="entries">
            <option value="10" selected>10 {{ __('per page') }}</option>
            <option value="25">25 {{ __('per page') }}</option>
            <option value="50">50 {{ __('per page') }}</option>
            <option value="100">100 {{ __('per page') }}</option>
          </select>
        </div>
        <div class="col-md-1">
          <button type="button" id="clear" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center" title="{{ __('Reset Filters') }}">
            <i class="bx bx-refresh fs-5"></i>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- 2. Fuel Receipts Table Card -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-gas-pump fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Fuel Purchases List') }}</h5>
        <small class="text-muted">{{ __('Total Vouchers:') }} <strong class="text-primary">{{ $fuelStations->total() }}</strong></small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button id="submit-selected" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1 shadow-sm" disabled>
        <i class="bx bx-check-double"></i>
        <span>{{ __('Mark Selected as Paid') }}</span>
      </button>
    </div>
  </div>

  @include('content.Fuelstation.add')

  <div class="table-responsive text-nowrap">
    <div id="content">
      @include('content.Fuelstation.pagination-data', ['fuelStations' => $fuelStations])
    </div>
  </div>

  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center py-3 border-top gap-2">
    <div class="small text-muted">
      {{ __('Showing') }} <span class="fw-semibold">{{ $fuelStations->firstItem() ?? 0 }}</span> {{ __('to') }} <span class="fw-semibold">{{ $fuelStations->lastItem() ?? 0 }}</span> {{ __('of') }} <span class="fw-semibold">{{ $fuelStations->total() }}</span>
    </div>
    <div id="pagination" class="pagination-wrapper mb-0">
      {{ $fuelStations->links('vendor.pagination.custom') }}
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const page = url.searchParams.get("page");
        fetchContent(page);
    });

    $('#search').on('keyup', function() {
        fetchContent();
    });

    $('#entries').on('change', function() {
        fetchContent();
    });

    $('#clear').on('click', function() {
        $('#search').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        fetchContent();
    });

    $('#start_date, #end_date').on('change', function() {
        fetchContent();
    });

    function fetchContent(page = 1) {
        const search = $('#search').val();
        const entries = $('#entries').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        $.ajax({
            url: "{{ route('fuel-stations.index') }}",
            type: "GET",
            data: {
                page: page,
                search: search,
                per_page: entries,
                start_date: startDate,
                end_date: endDate,
            },
            success: function(response) {
                $('#content').fadeOut(100, function() {
                    $(this).html(response.content).fadeIn(100);
                });
                $('#pagination').html(response.pagination);
                $('.total-amount').html(response.total);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    // Handle "Select All" checkbox
    $(document).on('change', '#select-all-page', function() {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
        $('#submit-selected').prop('disabled', !isChecked);
    });

    // Update "Select All" when individual checkboxes change
    $(document).on('change', '.row-checkbox', function() {
        var anyChecked = $('.row-checkbox:checked').length > 0;
        $('#submit-selected').prop('disabled', !anyChecked);
    });

    // Submit selected IDs via AJAX
    $('#submit-selected').on('click', function(e) {
        e.preventDefault();

        var selectedIds = $('.row-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            return;
        }

        Swal.fire({
            title: "{{ __('Have the documents been paid?') }}",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Mark as Paid') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('fuel-stations.update.status') }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "{{ __('Saved!') }}",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
});
</script>
@endsection
