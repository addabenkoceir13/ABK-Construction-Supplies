@extends('layouts/contentNavbarLayout')

@section('title', __('الملف الشخصي وإعدادات الحساب') . ' - ' . config('variables.templateName'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y p-0 profile-wrapper">

  {{-- Top Navigation Breadcrumb --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h4 class="fw-bold mb-1 text-heading d-flex align-items-center gap-2">
        <i class="bx bx-user-circle text-primary fs-3"></i>
        <span>{{ __('الملف الشخصي وإعدادات الحساب') }}</span>
      </h4>
      <p class="text-muted mb-0 small">
        {{ __('إدارة معلوماتك الشخصية، بيانات الدخول، وإعدادات الأمان الخاصة بحسابك') }}
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-label-primary px-3 py-2 rounded-pill d-flex align-items-center gap-1 fs-tiny fw-semibold">
        <i class="bx bxs-shield text-primary"></i>
        <span>{{ __('مسؤول النظام (Admin)') }}</span>
      </span>
      <a href="{{ route('dashboard-analytics') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
        <i class="bx bx-arrow-back"></i>
        <span>{{ __('الرئيسية') }}</span>
      </a>
    </div>
  </div>

  {{-- Flash Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible shadow-xs d-flex align-items-center gap-2 mb-4 border-0" role="alert">
      <i class="bx bx-check-circle fs-4 text-success flex-shrink-0"></i>
      <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
      <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible shadow-xs d-flex align-items-center gap-2 mb-4 border-0" role="alert">
      <i class="bx bx-error-circle fs-4 text-danger flex-shrink-0"></i>
      <div class="flex-grow-1">
        <strong class="d-block mb-1">{{ __('يرجى تصحيح الأخطاء التالية:') }}</strong>
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- 1. HERO HEADER PROFILE CARD --}}
  <div class="card profile-hero-card mb-4 border-0 shadow-sm overflow-hidden">
    {{-- Decorative Banner --}}
    <div class="profile-hero-banner position-relative">
      <div class="profile-banner-shapes">
        <div class="shape-circle-1"></div>
        <div class="shape-circle-2"></div>
      </div>
    </div>

    {{-- User Identity Info Bar --}}
    <div class="card-body profile-hero-body pt-0 px-4 pb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-end gap-3 profile-hero-content">
        {{-- Avatar & Core Details --}}
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-3 text-center text-sm-start profile-user-meta">
          <div class="position-relative profile-avatar-box">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle profile-main-avatar shadow-lg border-4" id="mainAvatarPreview">
            <label for="avatarInputTrigger" class="profile-avatar-badge position-absolute bottom-0 end-0 rounded-circle shadow-sm" title="{{ __('تغيير الصورة الشخصية') }}">
              <i class="bx bx-camera text-white"></i>
            </label>
          </div>

          <div class="profile-title-details mb-2">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
              <h4 class="mb-0 fw-bold text-heading">{{ $user->display_name }}</h4>
              @if($user->username)
                <span class="badge bg-label-info rounded-pill px-2 py-1 fs-tiny fw-bold">
                  {{ '@' . $user->username }}
                </span>
              @endif
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-3 text-muted small">
              <span class="d-flex align-items-center gap-1">
                <i class="bx bx-envelope text-primary"></i> {{ $user->email }}
              </span>
              @if($user->phone)
                <span class="d-flex align-items-center gap-1">
                  <i class="bx bx-phone text-success"></i> {{ $user->phone }}
                </span>
              @endif
              <span class="d-flex align-items-center gap-1">
                <i class="bx bx-calendar text-warning"></i> {{ __('عضو منذ') }} {{ $user->created_at ? $user->created_at->format('Y/m/d') : '2024' }}
              </span>
            </div>
          </div>
        </div>

        {{-- Quick Stats Pills --}}
        <div class="d-flex flex-wrap justify-content-center gap-2 profile-stats-grid">
          <div class="profile-stat-chip px-3 py-2 rounded-3 text-center">
            <span class="d-block fw-bold fs-5 text-primary">{{ $stats['total_debts'] ?? 0 }}</span>
            <small class="text-muted fs-tiny">{{ __('إجمالي الديون') }}</small>
          </div>
          <div class="profile-stat-chip px-3 py-2 rounded-3 text-center">
            <span class="d-block fw-bold fs-5 text-danger">{{ $stats['unpaid_debts'] ?? 0 }}</span>
            <small class="text-muted fs-tiny">{{ __('ديون معلقة') }}</small>
          </div>
          <div class="profile-stat-chip px-3 py-2 rounded-3 text-center">
            <span class="d-block fw-bold fs-5 text-info">{{ $stats['vehicles_count'] ?? 0 }}</span>
            <small class="text-muted fs-tiny">{{ __('المركبات') }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. TABBED SETTINGS PANELS --}}
  <div class="row g-4">
    {{-- Navigation Tabs --}}
    <div class="col-12">
      <div class="nav-align-top">
        <ul class="nav nav-pills mb-3 gap-2 profile-nav-pills" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link {{ session('active_tab') !== 'password' ? 'active' : '' }} d-flex align-items-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#navs-personal-info" aria-controls="navs-personal-info" aria-selected="{{ session('active_tab') !== 'password' ? 'true' : 'false' }}">
              <i class="bx bx-user fs-5"></i>
              <span>{{ __('المعلومات الشخصية') }}</span>
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link {{ session('active_tab') === 'password' ? 'active' : '' }} d-flex align-items-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#navs-security" aria-controls="navs-security" aria-selected="{{ session('active_tab') === 'password' ? 'true' : 'false' }}">
              <i class="bx bx-shield-quarter fs-5"></i>
              <span>{{ __('الأمان وكلمة المرور') }}</span>
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link d-flex align-items-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#navs-overview" aria-controls="navs-overview" aria-selected="false">
              <i class="bx bx-info-circle fs-5"></i>
              <span>{{ __('تفاصيل الحساب والنظام') }}</span>
            </button>
          </li>
        </ul>

        <div class="tab-content p-0 bg-transparent border-0 shadow-none">
          {{-- TAB 1: PERSONAL INFORMATION --}}
          <div class="tab-pane fade {{ session('active_tab') !== 'password' ? 'show active' : '' }}" id="navs-personal-info" role="tabpanel">
            <div class="card border-0 shadow-sm profile-section-card">
              <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                    <i class="bx bx-edit-alt fs-5"></i>
                  </div>
                  <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">{{ __('تعديل البيانات الشخصية') }}</h5>
                    <small class="text-muted">{{ __('قم بتحديث اسم المستخدم، الاسم الكامل، الهاتف والبريد الإلكتروني') }}</small>
                  </div>
                </div>
              </div>

              <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                  @csrf
                  @method('PUT')

                  {{-- Hidden file input triggered by avatar buttons --}}
                  <input type="file" name="avatar" id="avatarInputTrigger" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp">

                  {{-- Avatar Upload Section Card --}}
                  <div class="profile-photo-dropzone p-3 rounded-3 mb-4 border d-flex flex-column flex-sm-row align-items-center gap-3">
                    <div class="avatar avatar-xl position-relative">
                      <img src="{{ $user->avatar_url }}" alt="avatar" class="rounded-circle object-fit-cover w-100 h-100 border shadow-xs" id="avatarSecondaryPreview">
                    </div>
                    <div class="flex-grow-1 text-center text-sm-start">
                      <h6 class="fw-bold mb-1 text-heading">{{ __('صورة الحساب الشخصي') }}</h6>
                      <p class="text-muted small mb-2">{{ __('يدعم صيغ JPG، PNG، WEBP أو GIF بحجم أقصى 3 ميجابايت.') }}</p>
                      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                        <label for="avatarInputTrigger" class="btn btn-sm btn-primary d-flex align-items-center gap-1 mb-0 cursor-pointer shadow-xs">
                          <i class="bx bx-upload"></i>
                          <span>{{ __('رفع صورة جديدة') }}</span>
                        </label>
                        @if($user->avatar)
                          <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" onclick="document.getElementById('formRemoveAvatar').submit();">
                            <i class="bx bx-trash"></i>
                            <span>{{ __('حذف الصورة') }}</span>
                          </button>
                        @endif
                      </div>
                    </div>
                  </div>

                  {{-- Input Fields Grid --}}
                  <div class="row g-3">
                    {{-- First Name --}}
                    <div class="col-md-6">
                      <label for="fname" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('الاسم الأول (First Name)') }}
                      </label>
                      <div class="input-group input-group-merge custom-profile-input">
                        <span class="input-group-text"><i class="bx bx-user text-primary"></i></span>
                        <input type="text" id="fname" name="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname', $user->fname) }}" placeholder="{{ __('الاسم الأول...') }}" autocomplete="given-name" />
                      </div>
                      @error('fname')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-6">
                      <label for="lname" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('اسم العائلة / اللقب (Last Name)') }}
                      </label>
                      <div class="input-group input-group-merge custom-profile-input">
                        <span class="input-group-text"><i class="bx bx-id-card text-primary"></i></span>
                        <input type="text" id="lname" name="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname', $user->lname) }}" placeholder="{{ __('اسم العائلة / اللقب...') }}" autocomplete="family-name" />
                      </div>
                      @error('lname')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                      <label for="username" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('اسم المستخدم (Username)') }} <span class="text-danger">*</span>
                      </label>
                      <div class="input-group input-group-merge custom-profile-input">
                        <span class="input-group-text fw-bold text-info">@</span>
                        <input type="text" id="username" name="username" class="form-control ps-1 @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" placeholder="username" autocomplete="username" dir="ltr" />
                      </div>
                      <small class="text-muted fs-tiny d-block mt-1">{{ __('يستخدم لتسجيل الدخول أو الإشارة إلى حسابك') }}</small>
                      @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-md-6">
                      <label for="phone" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('رقم الهاتف (Phone Number)') }}
                      </label>
                      <div class="input-group input-group-merge custom-profile-input">
                        <span class="input-group-text"><i class="bx bx-phone text-success"></i></span>
                        <input type="tel" id="phone" name="phone" class="form-control ps-1 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="0655 44 33 22" dir="ltr" />
                      </div>
                      @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="col-md-12">
                      <label for="email" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('البريد الإلكتروني (Email Address)') }} <span class="text-danger">*</span>
                      </label>
                      <div class="input-group input-group-merge custom-profile-input">
                        <span class="input-group-text"><i class="bx bx-envelope text-warning"></i></span>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="admin@example.com" required dir="ltr" />
                      </div>
                      @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  {{-- Action Buttons --}}
                  <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                    <button type="reset" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                      <i class="bx bx-reset"></i>
                      <span>{{ __('إعادة تعيين') }}</span>
                    </button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                      <i class="bx bx-save"></i>
                      <span>{{ __('حفظ التعديلات') }}</span>
                    </button>
                  </div>
                </form>

                {{-- Hidden Form for Avatar Removal --}}
                <form action="{{ route('profile.avatar.remove') }}" method="POST" id="formRemoveAvatar" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </div>
          </div>

          {{-- TAB 2: SECURITY & PASSWORD --}}
          <div class="tab-pane fade {{ session('active_tab') === 'password' ? 'show active' : '' }}" id="navs-security" role="tabpanel">
            <div class="card border-0 shadow-sm profile-section-card">
              <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar avatar-sm bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
                    <i class="bx bx-lock-alt fs-5"></i>
                  </div>
                  <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">{{ __('تغيير كلمة المرور') }}</h5>
                    <small class="text-muted">{{ __('تأكد من اختيار كلمة مرور قوية لحماية حسابك') }}</small>
                  </div>
                </div>
              </div>

              <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                  @csrf
                  @method('PUT')

                  <div class="row g-3">
                    {{-- Current Password --}}
                    <div class="col-md-12">
                      <label for="current_password" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('كلمة المرور الحالية') }} <span class="text-danger">*</span>
                      </label>
                      <div class="input-group input-group-merge custom-profile-input form-password-toggle">
                        <span class="input-group-text"><i class="bx bx-key text-warning"></i></span>
                        <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required dir="ltr" />
                        <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
                      </div>
                      @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="col-md-6">
                      <label for="password" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('كلمة المرور الجديدة') }} <span class="text-danger">*</span>
                      </label>
                      <div class="input-group input-group-merge custom-profile-input form-password-toggle">
                        <span class="input-group-text"><i class="bx bx-lock-open text-primary"></i></span>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required dir="ltr" />
                        <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
                      </div>
                      <small class="text-muted fs-tiny d-block mt-1">{{ __('يجب ألا تقل عن 8 أحرف وأرقام.') }}</small>
                      @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                      <label for="password_confirmation" class="form-label fw-semibold text-heading small mb-1">
                        {{ __('تأكيد كلمة المرور الجديدة') }} <span class="text-danger">*</span>
                      </label>
                      <div class="input-group input-group-merge custom-profile-input form-password-toggle">
                        <span class="input-group-text"><i class="bx bx-lock text-success"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required dir="ltr" />
                        <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
                      </div>
                    </div>
                  </div>

                  {{-- Security Advisory Box --}}
                  <div class="p-3 rounded-3 border mt-4 profile-security-advisory">
                    <h6 class="fw-bold mb-2 text-heading small d-flex align-items-center gap-2">
                      <i class="bx bx-shield-alt-2 text-info"></i>
                      <span>{{ __('إرشادات أمان كلمة المرور:') }}</span>
                    </h6>
                    <ul class="mb-0 text-muted small ps-3">
                      <li>{{ __('استخدم 8 أحرف على الأقل تحتوي على مزيج من الأحرف الكبيرة والصغيرة والأرقام.') }}</li>
                      <li>{{ __('تجنب استخدام كلمات مرور مستخدمة في مواقع أخرى أو معلومات شخصية سهلة التخمين.') }}</li>
                    </ul>
                  </div>

                  {{-- Action Button --}}
                  <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                      <i class="bx bx-check-shield"></i>
                      <span>{{ __('تحديث كلمة المرور') }}</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          {{-- TAB 3: ACCOUNT & SYSTEM DETAILS --}}
          <div class="tab-pane fade" id="navs-overview" role="tabpanel">
            <div class="row g-4">
              {{-- Account Credentials Summary --}}
              <div class="col-md-6">
                <div class="card border-0 shadow-sm profile-section-card h-100">
                  <div class="card-header border-bottom py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold text-heading d-flex align-items-center gap-2">
                      <i class="bx bx-id-card text-primary"></i>
                      <span>{{ __('بيانات الحساب التقنية') }}</span>
                    </h5>
                  </div>
                  <div class="card-body p-4">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                      <li class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">{{ __('معرّف الحساب (ID):') }}</span>
                        <span class="fw-bold text-heading font-monospace">#{{ $user->id }}</span>
                      </li>
                      <li class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">{{ __('الرتبة في النظام:') }}</span>
                        <span class="badge bg-label-primary rounded-pill">{{ __('مدير رئيسي (Super Admin)') }}</span>
                      </li>
                      <li class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">{{ __('حالة الحساب:') }}</span>
                        <span class="badge bg-label-success rounded-pill d-flex align-items-center gap-1">
                          <i class="bx bxs-check-circle fs-tiny"></i>
                          <span>{{ __('نشط وموثق') }}</span>
                        </span>
                      </li>
                      <li class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small">{{ __('تاريخ إنشاء الحساب:') }}</span>
                        <span class="fw-semibold text-heading">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-' }}</span>
                      </li>
                      <li class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">{{ __('آخر تحديث:') }}</span>
                        <span class="fw-semibold text-heading">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              {{-- System Preferences --}}
              <div class="col-md-6">
                <div class="card border-0 shadow-sm profile-section-card h-100">
                  <div class="card-header border-bottom py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold text-heading d-flex align-items-center gap-2">
                      <i class="bx bx-slider-alt text-info"></i>
                      <span>{{ __('تفضيلات النظام والمظهر') }}</span>
                    </h5>
                  </div>
                  <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                      <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-0 fw-bold text-heading">{{ __('وضع المظهر (Theme)') }}</h6>
                          <small class="text-muted">{{ __('التبديل بين الوضع المظلم والفاتح') }}</small>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="{{ url('theme/light') }}" class="btn {{ Session::get('theme') !== 'dark' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bx bx-sun me-1"></i>{{ __('فاتح') }}
                          </a>
                          <a href="{{ url('theme/dark') }}" class="btn {{ Session::get('theme') === 'dark' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bx bx-moon me-1"></i>{{ __('داكن') }}
                          </a>
                        </div>
                      </div>

                      <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-0 fw-bold text-heading">{{ __('لغة الواجهة (Language)') }}</h6>
                          <small class="text-muted">{{ __('العربية أو الفرنسية') }}</small>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="{{ url('lang/ar') }}" class="btn {{ Session::get('locale') !== 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ __('العربية') }}
                          </a>
                          <a href="{{ url('lang/en') }}" class="btn {{ Session::get('locale') === 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ __('Français') }}
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Scoped CSS for Senior Designer Profile Page (Dark & Light Mode Compatible) --}}
<style>
  .profile-wrapper {
    animation: profileFadeIn 0.3s ease-in-out;
  }

  /* Profile Hero Card */
  .profile-hero-card {
    border-radius: 1rem !important;
  }

  .profile-hero-banner {
    height: 140px;
    background: linear-gradient(135deg, #696cff 0%, #3f42b6 60%, #1e2058 100%);
    overflow: hidden;
  }

  .profile-banner-shapes .shape-circle-1 {
    position: absolute;
    top: -50px;
    right: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
  }

  .profile-banner-shapes .shape-circle-2 {
    position: absolute;
    bottom: -60px;
    left: 20%;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(3, 195, 236, 0.25) 0%, rgba(3, 195, 236, 0) 70%);
    border-radius: 50%;
  }

  .profile-hero-body {
    position: relative;
    margin-top: -50px;
  }

  .profile-avatar-box {
    width: 104px;
    height: 104px;
  }

  .profile-main-avatar {
    width: 104px;
    height: 104px;
    object-fit: cover;
    border-color: #fff !important;
  }

  .profile-avatar-badge {
    width: 32px;
    height: 32px;
    background: #696cff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 2px solid #fff;
    transition: all 0.2s ease;
  }

  .profile-avatar-badge:hover {
    background: #5659ea;
    transform: scale(1.1);
  }

  .profile-stat-chip {
    min-width: 95px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .profile-stat-chip:hover {
    transform: translateY(-2px);
  }

  /* Nav Pills */
  .profile-nav-pills .nav-link {
    font-weight: 600;
    border-radius: 0.65rem;
    padding: 0.6rem 1.25rem;
    transition: all 0.2s ease;
  }

  .profile-nav-pills .nav-link:not(.active):hover {
    background-color: rgba(105, 108, 255, 0.08);
  }

  /* Section Cards */
  .profile-section-card {
    border-radius: 1rem !important;
  }

  .custom-profile-input .input-group-text {
    border-radius: 0.5rem;
  }

  .cursor-pointer {
    cursor: pointer;
  }

  /* ========================================================
     --- DARK MODE RULES ---
     ======================================================== */
  .dark-style .profile-hero-card,
  .dark-style .profile-section-card {
    background-color: #2b2c40 !important;
    border: 1px solid #444564 !important;
  }

  .dark-style .profile-main-avatar {
    border-color: #2b2c40 !important;
  }

  .dark-style .profile-avatar-badge {
    border-color: #2b2c40 !important;
  }

  .dark-style .profile-stat-chip {
    background-color: #32344d !important;
    border: 1px solid #444564 !important;
  }

  .dark-style .profile-photo-dropzone,
  .dark-style .profile-security-advisory,
  .dark-style .profile-section-card .border {
    background-color: rgba(255, 255, 255, 0.03) !important;
    border-color: #444564 !important;
  }

  .dark-style .profile-section-card .card-header,
  .dark-style .profile-section-card .border-bottom,
  .dark-style .profile-section-card .border-top {
    border-color: #444564 !important;
  }

  .dark-style .custom-profile-input .input-group-text {
    background-color: #32344d !important;
    border-color: #444564 !important;
    color: #a1acb8 !important;
  }

  .dark-style .custom-profile-input .form-control {
    background-color: #2b2c40 !important;
    border-color: #444564 !important;
    color: #cbc8e0 !important;
  }

  .dark-style .custom-profile-input .form-control:focus {
    background-color: #2b2c40 !important;
    border-color: #696cff !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.2) !important;
  }

  .dark-style .custom-profile-input ::placeholder {
    color: #7071a4 !important;
  }

  /* ========================================================
     --- LIGHT MODE RULES ---
     ======================================================== */
  html:not(.dark-style) .profile-hero-card,
  html:not(.dark-style) .profile-section-card {
    background-color: #ffffff !important;
    border: 1px solid #e7e7e8 !important;
  }

  html:not(.dark-style) .profile-main-avatar {
    border-color: #ffffff !important;
  }

  html:not(.dark-style) .profile-avatar-badge {
    border-color: #ffffff !important;
  }

  html:not(.dark-style) .profile-stat-chip {
    background-color: #f8f9fa !important;
    border: 1px solid #e7e7e8 !important;
  }

  html:not(.dark-style) .profile-photo-dropzone,
  html:not(.dark-style) .profile-security-advisory,
  html:not(.dark-style) .profile-section-card .border {
    background-color: #fbfbfd !important;
    border-color: #e7e7e8 !important;
  }

  html:not(.dark-style) .profile-section-card .card-header,
  html:not(.dark-style) .profile-section-card .border-bottom,
  html:not(.dark-style) .profile-section-card .border-top {
    border-color: #e7e7e8 !important;
  }

  html:not(.dark-style) .custom-profile-input .input-group-text {
    background-color: #f8f9fa !important;
    border-color: #d9dee3 !important;
    color: #566a7f !important;
  }

  html:not(.dark-style) .custom-profile-input .form-control {
    background-color: #ffffff !important;
    border-color: #d9dee3 !important;
    color: #566a7f !important;
  }

  html:not(.dark-style) .custom-profile-input .form-control:focus {
    border-color: #696cff !important;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15) !important;
  }

  @keyframes profileFadeIn {
    0% { opacity: 0; transform: translateY(8px); }
    100% { opacity: 1; transform: translateY(0); }
  }
</style>

{{-- Client-Side Instant Avatar Preview & Password Eye Toggle Script --}}
@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Instant Avatar Preview
    const avatarInput = document.getElementById('avatarInputTrigger');
    const mainPreview = document.getElementById('mainAvatarPreview');
    const secondaryPreview = document.getElementById('avatarSecondaryPreview');

    if (avatarInput) {
      avatarInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(event) {
            if (mainPreview) mainPreview.src = event.target.result;
            if (secondaryPreview) secondaryPreview.src = event.target.result;
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    }

    // 2. Password Visibility Toggles
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bx-hide');
          icon.classList.add('bx-show');
        } else {
          input.type = 'password';
          icon.classList.remove('bx-show');
          icon.classList.add('bx-hide');
        }
      });
    });
  });
</script>
@endsection

@endsection
