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
                <p>{{ number_format($total, 2) }} {{ __('DZ') }}</p>
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
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="10">10</option>
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

@section('page-script')
    <script>
        $(document).ready(function() {
            // var currentPage = new URLSearchParams(window.location.search).get('page') || 1;
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault(); // Prevent default link behavior
                let page = $(this).attr('href').split('page=')[1]; // Get page number from the URL
                let search = $('#search').val(); // Get the search value
                let start_date = $('#start_date').val(); // Optional: Add start_date if needed
                let end_date = $('#end_date').val(); // Optional: Add end_date if needed

                let params = {
                    page: page,
                };

                let queryString = $.param(params);

                console.log('====================================');
                console.log(queryString);
                console.log('====================================');

                fetchContent(page, search, start_date, end_date);
            });
            // Event listener for search input
            $('#search').on('keyup', function() {
                fetchContent(); // Fetch results on search
            });

            // Event listener for entries per page dropdown
            $('#entries').on('change', function() {
                fetchContent(); // Fetch results on entries change
            });

            // Pagination click event
            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchContent(page); // Fetch results for the selected page
            });

            // Clear filters
            $('#clear').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                fetchContent(); // Fetch with cleared filters
            });

            // Dynamic fetching function
            function fetchContent(page = 1, search = '', startDate = '', endDate = '') {
                // const search = $('#search').val();
                const entries = $('#entries').val();
                // const startDate = $('#start_date').val();
                // const endDate = $('#end_date').val();

                console.log('====================================');
                console.log('page:', page);
                console.log('entries:', entries);
                console.log('====================================');

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
@endsection



{{-- <script>
  $(document).ready(function() {
     // Initialize variables
      let searchQuery = '';
      let perPage = 10;

      // $(document).on('click', '.pagination a', function (event) {
      //     event.preventDefault();
      //     let page = $(this).attr('href').split('page=')[1];
      //     fetchFuelStations(page);
      // });

      function fetchFuelStations(page) {
          $.ajax({
              url: "?page=" + page,
              beforeSend: function () {
              $('#fuel-stations-container').fadeOut(); // Fade out the old content
            },
            success: function (data) {
                $('#fuel-stations-container').html(data).fadeIn(); // Fade in the new content
            }
          });
      }

      // Fetch content dynamically
      function fetchContent(page = 1) {
          $('#content').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
          var searchQuery = $('#search').val();  // Get the search query
          var perPage = $('#entries-per-page').val();  // Get the selected entries per page
          $.ajax({
              url: "{{ route('fuel-stations.index') }}", // Adjust the route as needed
              type: "GET",
              data: {
                  page: page,
                  search: searchQuery,
                  per_page: perPage,
              },
              success: function (response) {
                  // Update content and pagination dynamically
                  $('#content').fadeOut(200, function () {
                      $(this).html(response.content).fadeIn(200);
                  });

                  $('#pagination').html(response.pagination);
              },
              error: function (xhr, status, error) {
                  console.error('Error:', error);
              }
          });
      }

     // Event listener for search input
    $('#search').on('keyup', function () {
        fetchPage();  // Fetch the first page with the updated search query
    });

    // Event listener for entries per page dropdown
    $('#entries-per-page').on('change', function () {
        fetchPage();  // Fetch the first page with the updated entries per page
    });

      // Pagination Click Event
      $(document).on('click', '.page-link[data-page]', function (e) {
          e.preventDefault();
          const page = $(this).data('page');
          fetchContent(page); // Fetch results for the selected page
      });

      $(document).on('click', '#clear', function () {
        $('#start_date').val('');
        $('#end_date').val('');

        // Update the URL to remove date parameters
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');

        // Push the updated URL to the browser history
        history.pushState(null, '', url);

        // Optionally submit the form if required to refresh results
        $('#filter-form').submit();
      })
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const filterForm = document.getElementById('filter-form');
      const startDate = document.getElementById('start_date');
      const endDate = document.getElementById('end_date');

      startDate.addEventListener('change', function () {
          filterForm.submit();
      });

      endDate.addEventListener('change', function () {
          filterForm.submit();
      });
  });

</script> --}}
