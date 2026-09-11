@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');

@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme" id="layout-navbar">
  @endif
  @if(isset($navbarDetached) && $navbarDetached == '')
  <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{$containerNav}}">
      @endif

      <!--  Brand demo (display only for navbar-full and hide on below xl) -->
      @if(isset($navbarFull))
      <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{url('/')}}" class="app-brand-link gap-2 align-items-center">
          <span class="app-brand-logo demo d-flex align-items-center">
            <img src="{{ asset('assets/logo/abk-bg-transparent.jpeg') }}" alt="ABK Logo" style="height: 34px; width: auto; max-width: 40px; object-fit: contain; border-radius: 6px;">
          </span>
          <span class="app-brand-text demo menu-text fw-bolder">{{config('variables.templateName')}}</span>
        </a>
      </div>
      @endif

      <!-- ! Not required for layout-without-menu -->
      @if(!isset($navbarHideToggle))
      <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
          <i class="bx bx-menu bx-sm"></i>
        </a>
      </div>
      @endif

      <!-- Mobile Brand Logo (visible on mobile/tablet screens when menu is collapsed) -->
      <div class="d-flex d-xl-none align-items-center me-3">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
          <img src="{{ asset('assets/logo/abk-bg-transparent.jpeg') }}" alt="ABK Logo" style="height: 32px; width: auto; max-width: 38px; object-fit: contain; border-radius: 6px;">
          <span class="fw-bold text-heading d-none d-sm-inline" style="font-size: 0.92rem;">
            {{ config('app.locale') == 'en' ? 'ABK Supplies' : 'ع.ب.ق للبناء' }}
          </span>
        </a>
      </div>

      <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
          <div class="nav-item d-flex align-items-center">
            <i class="bx bx-search fs-4 lh-0"></i>
            <input type="text" class="form-control border-0 shadow-none" placeholder="{{ __('Search...') }}" aria-label="Search...">
          </div>
        </div>
        <!-- /Search -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">

          <!-- Place this tag where you want the button to render. -->
          {{-- <li class="nav-item lh-1 me-3">
            <a class="github-button" href="https://github.com/themeselection/sneat-html-laravel-admin-template-free" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star themeselection/sneat-html-laravel-admin-template-free on GitHub">Star</a>
          </li> --}}

          <!-- Language Direct Switch Button -->
          <li class="nav-item me-2 me-sm-3 d-flex align-items-center">
            <div class="nav-lang-segmented-switch" 
                 onclick="if(event.target === this) { window.location.href = '{{ Session::get('locale') == 'ar' ? url('lang/en') : url('lang/ar') }}'; }"
                 title="{{ Session::get('locale') == 'ar' ? 'التبديل إلى الإنجليزية / Switch to English' : 'Switch to Arabic / التبديل إلى العربية' }}">
              <a href="{{ url('lang/ar') }}" 
                 class="lang-pill {{ Session::get('locale') == 'ar' ? 'active' : '' }}" 
                 title="العربية">
                <span>عربي</span>
              </a>
              <a href="{{ url('lang/en') }}" 
                 class="lang-pill {{ Session::get('locale') == 'en' ? 'active' : '' }}" 
                 title="English">
                <span>EN</span>
              </a>
            </div>
          </li>

          <!-- Font Size Direct Switcher (All Tables & Pages) -->
          <li class="nav-item me-2 me-sm-3 d-flex align-items-center">
            <div class="nav-font-size-switch" role="group" aria-label="{{ __('حجم الخط لجميع الجداول والصفحات') }}" title="{{ __('حجم الخط: A- صغير / A افتراضي / A+ كبير') }}">
              <button type="button" class="font-size-btn" data-size="sm" title="{{ __('خط أصغر / مضغوط (13px)') }}">
                <span>A-</span>
              </button>
              <button type="button" class="font-size-btn active" data-size="md" title="{{ __('خط افتراضي (15px)') }}">
                <span>A</span>
              </button>
              <button type="button" class="font-size-btn" data-size="lg" title="{{ __('خط أكبر وأوضح (17px)') }}">
                <span>A+</span>
              </button>
            </div>
          </li>

          <!-- Dark / Light Mode Direct Toggle Switch Button -->
          <li class="nav-item me-2 me-sm-3 d-flex align-items-center">
            <a href="{{ Session::get('theme') == 'dark' ? url('theme/light') : url('theme/dark') }}" 
               class="nav-theme-toggle-switch {{ Session::get('theme') == 'dark' ? 'is-dark' : 'is-light' }}" 
               title="{{ Session::get('theme') == 'dark' ? __('تبديل إلى الوضع الفاتح') : __('تبديل إلى الوضع الداكن') }}"
               aria-label="{{ __('Toggle Dark / Light Mode') }}">
              <span class="theme-toggle-track">
                <span class="theme-toggle-icon icon-sun"><i class="bx bx-sun"></i></span>
                <span class="theme-toggle-icon icon-moon"><i class="bx bx-moon"></i></span>
                <span class="theme-toggle-thumb">
                  @if (Session::get('theme') == 'dark')
                    <i class="bx bxs-moon" style="color: #F2A20C;"></i>
                  @else
                    <i class="bx bxs-sun text-warning"></i>
                  @endif
                </span>
              </span>
            </a>
          </li>

          <!-- User -->
          <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                <img src="{{ auth()->user()->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt="avatar" class="w-px-40 h-auto rounded-circle object-fit-cover">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="{{ route('profile.index') }}">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar avatar-online">
                        <img src="{{ auth()->user()->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt="avatar" class="w-px-40 h-auto rounded-circle object-fit-cover">
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <span class="fw-semibold d-block text-heading">{{ auth()->user()->display_name }}</span>
                      <small class="text-muted">
                        @if(auth()->user()->username)
                          {{ '@' . auth()->user()->username }}
                        @else
                          {{ __('Admin') }}
                        @endif
                      </small>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.index') }}">
                  <i class="bx bx-user me-2 text-primary"></i>
                  <span class="align-middle">{{ __('My Profile') }}</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.index') }}">
                  <i class='bx bx-cog me-2 text-secondary'></i>
                  <span class="align-middle">{{ __('Settings') }}</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);">
                  <span class="d-flex align-items-center align-middle">
                    <i class="flex-shrink-0 bx bx-credit-card me-2 pe-1"></i>
                    <span class="flex-grow-1 align-middle">{{ __('Billing') }}</span>
                    <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                  </span>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="{{ url('/auth/logout')}}">
                  <i class='bx bx-power-off me-2'></i>
                  <span class="align-middle">{{ __('Log Out') }}</span>
                </a>
              </li>
            </ul>
          </li>
          <!--/ User -->
        </ul>
      </div>

      @if(!isset($navbarDetached))
    </div>
    @endif
  </nav>
  <!-- / Navbar -->
