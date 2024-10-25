<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
      <a href="#" class="app-brand-link">
          <span class="app-brand-logo demo">
              <img width="25" src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="brand-logo" srcset="">
              {{-- @include('_partials.macros',["width"=>25,"withbg"=>'#696cff']) --}}
          </span>
          <span class="app-brand-text demo menu-text fw-bold text-capitalize ms-2">
              {{ config('app.locale') == 'en' ? config('variables.NameSiteFr') : config('variables.NameSiteAr')  }}
          </span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-autod-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
      <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Dashboard') }}</span>
      </li>

      <li class="menu-item {{ request()->routeIs('dashboard-analytics') ? 'active' : '' }}">
          <a href="{{ route('dashboard-analytics') }}" class="menu-link">
              {{-- <i class="menu-icon tf-icons bx bx-collection"></i> --}}
              <i class='menu-icon bx bxs-dashboard'></i>
              <div>{{ __('Dashboard') }}</div>
          </a>
      </li>

      <li class="menu-item {{ request()->routeIs('building-materals.index') ? 'active' : '' }}">
        <a href="{{ route('building-materals.index') }}" class="menu-link">
            <i class='menu-icon bx bx-cube-alt'></i>
            <div>{{ __('Building Materials') }}</div>
        </a>
      </li>

      <li class="menu-item {{ request()->routeIs('debt.*') ? 'active' : '' }} ">
        <a href="{{ route('debt.index') }}" class="menu-link">
            <i class='menu-icon bx bx-credit-card-front'></i>
            <div>{{ __('Debts') }}</div>
        </a>
      </li>

      <li class="menu-item {{ request()->routeIs('debt-supplier.*') ? 'active' : '' }} ">
        <a href="{{ route('debt-supplier.index') }}" class="menu-link">
            <i class='menu-icon bx bx-credit-card-front'></i>
            <div>{{ __('Debts Supplier') }}</div>
        </a>
      </li>
</aside>



{{-- !! old sidebar JS  --}}
{{-- <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @include('_partials.macros',["width"=>25,"withbg"=>'#696cff'])
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{config('variables.templateName')}}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-autod-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>



  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)


    @if (isset($menu->menuHeader))
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ $menu->menuHeader }}</span>
    </li>

    @else


    @php
    $activeClass = null;
    $currentRouteName = Route::currentRouteName();

    if ($currentRouteName === $menu->slug) {
    $activeClass = 'active';
    }
    elseif (isset($menu->submenu)) {
    if (gettype($menu->slug) === 'array') {
    foreach($menu->slug as $slug){
    if (str_contains($currentRouteName,$slug) and strpos($currentRouteName,$slug) === 0) {
    $activeClass = 'active open';
    }
    }
    }
    else{
    if (str_contains($currentRouteName,$menu->slug) and strpos($currentRouteName,$menu->slug) === 0) {
    $activeClass = 'active open';
    }
    }

    }
    @endphp


    <li class="menu-item {{$activeClass}}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
      </a>


      @isset($menu->submenu)
      @include('layouts.sections.menu.submenu',['menu' => $menu->submenu])
      @endisset
    </li>
    @endif
    @endforeach
  </ul>

</aside> --}}
