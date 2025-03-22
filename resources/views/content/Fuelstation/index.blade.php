@extends('layouts/contentNavbarLayout')

@section('title', __('Fuel Purchase Accounting'))

@section('content')

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Fuel Purchase Accounting') }} /</span> {{ __('Fuel Purchase Accounting') }}
    </h4>
    <!-- Basic Bootstrap Table -->
    <div class="card p-2">
        @php
            $total = 0;
            foreach ($fuelStations as $fuelStation) {
                $total = $total + $fuelStation->amount;
            }
        @endphp
        <h5 class="card-header d-flex justify-content-between">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddFuelReceipt">
                {{ __('Add Fuel Receipt') }}
            </button>
            <div class="d-flex">
                <p class="mx-2">{{ __('Total Amount: ') }}</p>
                <p class="total-amount">{{ number_format($total, 2) }} {{ __('DZ') }}</p>
            </div>
        </h5>
        @include('content.Fuelstation.add')

        <div class="card-body">
            <form id="filter-form" method="GET" action="{{ route('fuel-stations.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="col-sm-2 col-form-label" for="start_date">{{ __('From') }}</label>
                            <div class="col-sm-10">
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ request('start_date') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <label class="col-sm-2 col-form-label" for="end_date">{{ __('To') }}</label>
                            <div class="col-sm-10">
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ request('end_date') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="clear"
                            class="btn btn-outline-pinterest btn-sm mt-1">{{ __('Clean') }}</button>
                    </div>
                </div>
            </form>

            <form action="{{ route('fuel-stations.index') }}" method="get">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ __('Search.....') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <select class="form-select mb-3" id="entries">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <div class="mt-3">
                <button id="submit-selected" class="btn btn-primary m-2" disabled>{{ __('To be sure') }}</button>
            </div>

            <div id="content">
                @include('content.Fuelstation.pagination-data', ['fuelStations' => $fuelStations])
            </div>
            <div id="pagination" class="mt-3 pagination-wrapper">
                {{ $fuelStations->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->
@endsection

@section('page-style')

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = new URL($(this).attr('href')); // Proper URL parsing
                const page = url.searchParams.get("page"); // Get page parameter value
                fetchContent(page);
            });

            // Event listener for search input
            $('#search').on('keyup', function() {
                fetchContent(); // Fetch results on search
            });

            // Event listener for entries per page dropdown
            $('#entries').on('change', function() {
                fetchContent(); // Fetch results on entries change
            });

            // Clear filters
            $('#clear').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                fetchContent(); // Fetch with cleared filters
            });

            // Dynamic fetching function
            function fetchContent(page = 1) {
                const search = $('#search').val();
                const entries = $('#entries').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                console.log(page, entries, search);

                // $('#content').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

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
                        $('#content').fadeOut(200, function() {
                            $(this).html(response.content).fadeIn(200);
                        });
                        $('#pagination').html(response.pagination);
                        $('.total-amount').html(response.total);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // Auto-submit filters for date changes
            $('#start_date, #end_date').on('change', function() {
                fetchContent(); // Fetch results on date change
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Handle "Select All" checkbox
            $(document).on('change', '#select-all-page', function() {
                var isChecked = $(this).prop('checked');
                $('.row-checkbox').prop('checked', isChecked);
                document.getElementById('submit-selected').disabled = !isChecked;
            });

            // Update "Select All" when individual checkboxes change
            $(document).on('change', '.row-checkbox', function() {
                var allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
                var anyChecked = $('.row-checkbox:checked').length > 0 ? true : false;
                console.log(anyChecked);

                document.getElementById('submit-selected').disabled = !anyChecked;
            });

            // Submit selected IDs via AJAX
            $('#submit-selected').on('click', function(e) {
                e.preventDefault();

                var selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert('Please select at least one entry.');
                    return;
                }
                Swal.fire({
                    title: "{{ __('Have the documents been paid?') }}",
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: "{{ __('Yes') }}",
                    denyButtonText: "{{ __('No') }}",
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('fuel-stations.update.status') }}',
                            method: 'POST',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire("Saved!", "", "success");
                                window.location.reload();
                            },
                            error: function(xhr) {
                                // Handle error
                                alert('An error occurred. Please try again.');
                            }
                        });

                    } else if (result.isDenied) {
                        Swal.fire("Changes are not saved", "", "info");
                    }
                });


            });
        });
    </script>
@endsection
