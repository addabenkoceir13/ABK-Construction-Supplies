<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/fonts/boxicons.css')) }}" />

<!-- Core CSS -->
@if (Session::get('theme') == 'dark')
  @if (Session::get('locale') == 'ar')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default-dark.css') }}" />
  @else
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default-dark.css') }}" />
  @endif
@else
  @if (Session::get('locale') == 'ar')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
  @else
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
  @endif
@endif

<link rel="stylesheet" href="{{ asset(mix('assets/css/demo.css')) }}" />

<link rel="stylesheet" href="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')) }}" />

{{-- * DataTables CDN --}}
{{-- <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.7/af-2.7.0/b-3.1.2/date-1.5.4/fc-5.0.1/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.0/sp-2.3.2/sl-2.1.0/sr-1.4.1/datatables.min.css" rel="stylesheet"> --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.dataTables.css"> --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> --}}

<link rel="stylesheet" href="{{ asset('assets/DataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/DataTables/dataTables.dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/DataTables/jquery.dataTables.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/sweetalert2/min.css') }}">

<!-- Vendor Styles -->
@yield('vendor-style')


<!-- Page Styles -->
@yield('page-style')
