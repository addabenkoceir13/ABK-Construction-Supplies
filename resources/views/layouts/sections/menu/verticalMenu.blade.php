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

      <li class="menu-item {{ str_contains(request()->route()->getName(),'debt') ? 'active' : '' }} ">
        <a href="{{ route('debt.index') }}" class="menu-link">
            <i class='menu-icon bx bx-credit-card-front'></i>
            <div>{{ __('Debts') }}</div>
        </a>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'layouts') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-layout"></i>
            <div>{{ __('layouts') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('layouts-without-menu') ? 'active' : ''}}">
            <a href="{{ route('layouts-without-menu') }}" class="menu-link" >
                    <div>{{ __('Without menu') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('layouts-without-navbar') ? 'active' : ''}}">
            <a href="{{ route('layouts-without-navbar') }}" class="menu-link" >
                    <div>{{ __('Without navbar') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('layouts-container') ? 'active' : ''}}">
            <a href="{{ route('layouts-container') }}" class="menu-link" >
                    <div>{{ __('Container') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('layouts-fluid') ? 'active' : ''}}">
            <a href="{{ route('layouts-fluid') }}" class="menu-link" >
                    <div>{{ __('Fluid') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('layouts-blank') ? 'active' : ''}}">
            <a href="{{ route('layouts-blank') }}" class="menu-link" target="_blank">
                    <div>{{ __('Blank') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Pages') }}</span>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'pages-account-settings') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx bx-dock-top"></i>
            <div>{{ __('Account Settings') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('pages-account-settings-account') ? 'active' : ''}}">
            <a href="{{ route('pages-account-settings-account') }}" class="menu-link" >
                    <div>{{ __('Account') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('pages-account-settings-notifications') ? 'active' : ''}}">
            <a href="{{ route('pages-account-settings-notifications') }}" class="menu-link" >
                    <div>{{ __('Notifications') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('pages-account-settings-connections') ? 'active' : ''}}">
            <a href="{{ route('pages-account-settings-connections') }}" class="menu-link" >
                    <div>{{ __('Connections') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'auth') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
            <div>{{ __('Authentications') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('login') ? 'active' : ''}}">
            <a href="{{ route('login') }}" class="menu-link" target="_blank">
                    <div>{{ __('Login') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('auth-register-basic') ? 'active' : ''}}">
            <a href="{{ route('auth-register-basic') }}" class="menu-link" target="_blank">
                    <div>{{ __('Register') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('auth-reset-password-basic') ? 'active' : ''}}">
            <a href="{{ route('auth-reset-password-basic') }}" class="menu-link" target="_blank">
                    <div>{{ __('Forgot Password') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'pages-misc') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-cube-alt"></i>
            <div>{{ __('Misc') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('pages-misc-error') ? 'active' : ''}}">
            <a href="{{ route('pages-misc-error') }}" class="menu-link" target="_blank">
                    <div>{{ __('Error') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('pages-misc-under-maintenance') ? 'active' : ''}}">
            <a href="{{ route('pages-misc-under-maintenance') }}" class="menu-link" target="_blank">
                    <div>{{ __('Under Maintenance') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __('Components') }}</span>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'ui') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-box"></i>
            <div>{{ __('User interface') }} </div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('ui-accordion') ? 'active' : ''}}">
            <a href="{{ route('ui-accordion') }}" class="menu-link" >
                    <div>{{ __('Accordion') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('ui-alerts') ? 'active' : ''}}">
            <a href="{{ route('ui-alerts') }}" class="menu-link" >
                    <div>{{ __('Alerts') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-badges') ? 'active' : ''}}">
            <a href="{{ route('ui-badges') }}" class="menu-link" >
                    <div>{{ __('Badges') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-buttons') ? 'active' : ''}}">
            <a href="{{ route('ui-buttons') }}" class="menu-link" >
                    <div>{{ __('Buttons') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-carousel') ? 'active' : ''}}">
            <a href="{{ route('ui-carousel') }}" class="menu-link" >
                    <div>{{ __('Carousel') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-collapse') ? 'active' : ''}}">
            <a href="{{ route('ui-collapse') }}" class="menu-link" >
                    <div>{{ __('Collapse') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-dropdowns') ? 'active' : ''}}">
            <a href="{{ route('ui-dropdowns') }}" class="menu-link" >
                    <div>{{ __('Dropdowns') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-footer') ? 'active' : ''}}">
            <a href="{{ route('ui-footer') }}" class="menu-link" >
                    <div>{{ __('Footer') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-list-groups') ? 'active' : ''}}">
            <a href="{{ route('ui-list-groups') }}" class="menu-link" >
                    <div>{{ __('List groups') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-modals') ? 'active' : ''}}">
            <a href="{{ route('ui-modals') }}" class="menu-link" >
                    <div>{{ __('Modals') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-navbar') ? 'active' : ''}}">
            <a href="{{ route('ui-navbar') }}" class="menu-link" >
                    <div>{{ __('Navbar') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-offcanvas') ? 'active' : ''}}">
            <a href="{{ route('ui-offcanvas') }}" class="menu-link" >
                    <div>{{ __('Offcanvas') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-pagination-breadcrumbs') ? 'active' : ''}}">
            <a href="{{ route('ui-pagination-breadcrumbs') }}" class="menu-link" >
                    <div>{{ __('Pagination & Breadcrumbs') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-progress') ? 'active' : ''}}">
            <a href="{{ route('ui-progress') }}" class="menu-link" >
                    <div>{{ __('Progress') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-spinners') ? 'active' : ''}}">
            <a href="{{ route('ui-spinners') }}" class="menu-link" >
                    <div>{{ __('Spinners') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-tabs-pills') ? 'active' : ''}}">
            <a href="{{ route('ui-tabs-pills') }}" class="menu-link" >
                    <div>{{ __('Tabs & Pills') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-toasts') ? 'active' : ''}}">
            <a href="{{ route('ui-toasts') }}" class="menu-link" >
                    <div>{{ __('Toasts') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-tooltips-popovers') ? 'active' : ''}}">
            <a href="{{ route('ui-tooltips-popovers') }}" class="menu-link" >
                    <div>{{ __('Tooltips & popovers') }}</div>
            </a>
          </li>
          <li class="menu-item {{request()->routeIs('ui-typography') ? 'active' : ''}}">
            <a href="{{ route('ui-typography') }}" class="menu-link" >
                    <div>{{ __('Typography') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'extended') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-copy"></i>
            <div>{{ __('Extended UI') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('extended-ui-perfect-scrollbar') ? 'active' : ''}}">
            <a href="{{ route('extended-ui-perfect-scrollbar') }}" class="menu-link" >
                    <div>{{ __('Perfect scrollbar') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('extended-ui-text-divider') ? 'active' : ''}}">
            <a href="{{ route('extended-ui-text-divider') }}" class="menu-link" >
                    <div>{{ __('Text Divider') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ request()->routeIs('icons-boxicons') ? 'active' : '' }}">
        <a href="{{ route('icons-boxicons') }}" class="menu-link">
            {{-- <i class="menu-icon tf-icons bx bx-collection"></i> --}}
            <i class='menu-icon  tf-icons bx bx-crown'></i>
            <div>{{ __('Boxicons') }}</div>
        </a>
      </li>

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __('Forms & Tables') }}</span>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'forms') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-detail"></i>
            <div>{{ __('Form Elements') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('forms-basic-inputs') ? 'active' : ''}}">
            <a href="{{ route('forms-basic-inputs') }}" class="menu-link" >
                    <div>{{ __('Basic Inputs') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('forms-input-groups') ? 'active' : ''}}">
            <a href="{{ route('forms-input-groups') }}" class="menu-link" >
                    <div>{{ __('Input groups') }}</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ str_contains(request()->route()->getName(),'form-layouts') ? 'active open' : '' }}">
        <a class="menu-link menu-toggle" >
            <i class="menu-icon tf-icons bx bx-detail"></i>
            <div>{{ __('Form Layouts') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{request()->routeIs('form-layouts-vertical') ? 'active' : ''}}">
            <a href="{{ route('form-layouts-vertical') }}" class="menu-link" >
                    <div>{{ __('Vertical Form') }}</div>
            </a>
          </li>

          <li class="menu-item {{request()->routeIs('form-layouts-horizontal') ? 'active' : ''}}">
            <a href="{{ route('form-layouts-horizontal') }}" class="menu-link" >
                    <div>{{ __('Horizontal Form') }}</div>
            </a>
          </li>
        </ul>
      </li>


      <li class="menu-item {{ request()->routeIs('tables-basic') ? 'active' : '' }}">
          <a href="{{ route('tables-basic') }}" class="menu-link">
              <i class='menu-icon  tf-icons bx bx-table'></i>
              <div>{{ __('Tables') }}</div>
          </a>
      </li>

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">{{ __('Misc') }}</span>
      </li>

      <li class="menu-item {{ request()->routeIs('support') ? 'active' : '' }}">
          <a href="https://themeselection.com/support/" class="menu-link" target="_blank">
              <i class='menu-icon tf-icons bx bx-support'></i>
              <div>{{ __('Support') }}</div>
          </a>
      </li>

      <li class="menu-item {{ request()->routeIs('documentation') ? 'active' : '' }}">
          <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/laravel-introduction.html" class="menu-link" target="_blank">
              <i class="menu-icon tf-icons bx bx-file"></i>
              <div>{{ __('Documentation') }}</div>
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
