<!DOCTYPE html>

<html class="{{ (Session::get('theme') == 'dark') ? 'dark-style' : 'light-style' }} layout-menu-fixed" data-theme="{{ (Session::get('theme') == 'dark') ? 'dark' : 'theme-default' }}" data-bs-theme="{{ (Session::get('theme') == 'dark') ? 'dark' : 'light' }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="vertical-menu-laravel-template-free"
@if (Session::get('locale') == 'ar') dir="rtl" lang="ar" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  @if (config('app.locale') == 'en')
    <title>@yield('title') | {{ config('variables.NameSiteFr') }} </title>
  @else
    <title>@yield('title') | {{ config('variables.NameSiteAr') }} </title>
  @endif
  <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

  <!-- Include Styles -->
  @include('layouts/sections/styles')

  <!-- Include Scripts for customizer, helper, analytics, config -->
  @include('layouts/sections/scriptsIncludes')
</head>

<body>
  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  <!-- Include Scripts -->
  @include('layouts/sections/scripts')

</body>

</html>
