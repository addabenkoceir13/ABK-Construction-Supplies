@extends('layouts/blankLayout')

@section('title', __('Register') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
  /* ----------------------------------------------------
   * A.B.K Construction Supplies - Split-Screen Register Theme
   * Palette: Primary #D14A28, Warning #F2A20C, Info #4E9CC0
   * ---------------------------------------------------- */
  :root {
    --brand-primary: #D14A28;
    --brand-primary-hover: #b63e1f;
    --brand-accent: #F2A20C;
    --brand-info: #4E9CC0;
    --brand-card-bg: rgba(22, 28, 45, 0.92);
    --brand-card-border: rgba(255, 255, 255, 0.1);
    --brand-text-title: #ffffff;
    --brand-text-body: #cbd5e1;
    --brand-input-bg: rgba(15, 23, 42, 0.7);
    --brand-input-border: rgba(255, 255, 255, 0.16);
    --brand-panel-bg: #0b0f19;
  }

  .light-style {
    --brand-card-bg: rgba(255, 255, 255, 0.96);
    --brand-card-border: rgba(209, 74, 40, 0.15);
    --brand-text-title: #1e293b;
    --brand-text-body: #64748b;
    --brand-input-bg: #ffffff;
    --brand-input-border: #e2e8f0;
    --brand-panel-bg: #f8fafc;
  }

  .auth-split-wrapper {
    min-height: 100vh;
    display: flex;
    width: 100%;
    overflow-x: hidden;
    background-color: var(--brand-panel-bg);
    font-family: 'Public Sans', 'Cairo', sans-serif;
  }

  /* Hero Section */
  .auth-hero-half {
    flex: 1 1 50%;
    min-height: 100vh;
    position: relative;
    background: linear-gradient(135deg, #D14A28 0%, #b83d1e 40%, #F2A20C 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3.5rem 3rem;
    color: #ffffff;
    overflow: hidden;
    z-index: 1;
  }

  .auth-hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
      radial-gradient(rgba(255, 255, 255, 0.18) 1px, transparent 1px),
      radial-gradient(rgba(0, 0, 0, 0.12) 1px, transparent 1px);
    background-size: 32px 32px;
    background-position: 0 0, 16px 16px;
    opacity: 0.6;
    pointer-events: none;
  }

  .auth-hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
  }
  .auth-hero-orb-1 {
    width: 420px;
    height: 420px;
    top: -100px;
    left: -80px;
    background: rgba(242, 162, 12, 0.4);
  }
  .auth-hero-orb-2 {
    width: 380px;
    height: 380px;
    bottom: -80px;
    right: -60px;
    background: rgba(209, 74, 40, 0.5);
  }

  .hero-brand-header {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .hero-logo-box {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(12px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.85rem;
    color: #ffffff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
  }

  .hero-brand-name {
    font-size: 1.45rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
  }

  .hero-brand-sub {
    font-size: 0.85rem;
    opacity: 0.9;
    font-weight: 500;
    margin: 0;
  }

  .hero-center-content {
    position: relative;
    z-index: 2;
    max-width: 520px;
    margin: 2rem 0;
  }

  .hero-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .hero-headline {
    font-size: 2.35rem;
    font-weight: 800;
    line-height: 1.25;
    margin-bottom: 1.25rem;
    color: #ffffff;
  }

  .hero-description {
    font-size: 1.05rem;
    line-height: 1.6;
    opacity: 0.92;
    margin-bottom: 2rem;
  }

  .hero-features-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .hero-feature-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    font-size: 0.95rem;
    font-weight: 500;
  }

  .hero-feature-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.22);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }

  .hero-bottom-footer {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.85rem;
    opacity: 0.85;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 1.25rem;
  }

  /* Form Section */
  .auth-form-half {
    flex: 1 1 50%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2.5rem 3rem;
    position: relative;
    background-color: var(--brand-panel-bg);
  }

  .auth-top-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .auth-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 10px;
    background: rgba(209, 74, 40, 0.08);
    color: var(--brand-primary);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid rgba(209, 74, 40, 0.15);
  }

  .auth-action-btn:hover {
    background: #D14A28;
    color: #ffffff !important;
    transform: translateY(-1px);
  }

  .auth-form-container {
    max-width: 440px;
    width: 100%;
    margin: auto;
  }

  .auth-welcome-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--brand-text-title);
    margin-bottom: 0.5rem;
  }

  .auth-welcome-sub {
    color: var(--brand-text-body);
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }

  .auth-field-group {
    margin-bottom: 1.25rem;
  }

  .auth-field-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.45rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--brand-text-title);
  }

  .auth-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }

  .auth-input-icon {
    position: absolute;
    inset-inline-start: 14px;
    color: var(--brand-text-body);
    font-size: 1.25rem;
    pointer-events: none;
    z-index: 2;
    transition: color 0.2s ease;
  }

  .auth-input-control {
    width: 100%;
    height: 48px;
    background: var(--brand-input-bg);
    border: 1.5px solid var(--brand-input-border);
    border-radius: 12px;
    padding: 0 14px;
    padding-inline-start: 44px;
    color: var(--brand-text-title);
    font-size: 0.95rem;
    transition: all 0.2s ease;
  }

  .auth-input-control:focus {
    outline: none;
    border-color: #D14A28 !important;
    box-shadow: 0 0 0 0.25rem rgba(209, 74, 40, 0.22) !important;
  }

  .auth-input-control:focus + .auth-input-icon,
  .auth-input-wrapper:focus-within .auth-input-icon {
    color: #D14A28;
  }

  .auth-password-toggle {
    position: absolute;
    inset-inline-end: 12px;
    background: transparent;
    border: none;
    color: var(--brand-text-body);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
    z-index: 2;
  }

  .auth-password-toggle:hover {
    color: #D14A28;
  }

  .auth-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--brand-text-body);
    font-size: 0.88rem;
    user-select: none;
  }

  .auth-checkbox-input {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    accent-color: #D14A28;
    cursor: pointer;
  }

  .auth-checkbox-label a {
    color: #D14A28;
    text-decoration: none;
    font-weight: 600;
  }
  .auth-checkbox-label a:hover {
    text-decoration: underline;
  }

  .auth-submit-button {
    width: 100%;
    height: 50px;
    background: #D14A28;
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 8px 20px -4px rgba(209, 74, 40, 0.45);
    transition: all 0.25s ease;
    margin-top: 1.25rem;
  }

  .auth-submit-button:hover {
    background: #b63e1f;
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -4px rgba(209, 74, 40, 0.55);
  }

  .auth-form-footer {
    text-align: center;
    font-size: 0.9rem;
    color: var(--brand-text-body);
    padding-top: 1.5rem;
  }

  .auth-form-footer a {
    color: #D14A28;
    font-weight: 700;
    text-decoration: none;
  }

  .auth-form-footer a:hover {
    text-decoration: underline;
  }

  @media (max-width: 991.98px) {
    .auth-split-wrapper {
      flex-direction: column;
    }
    .auth-hero-half {
      min-height: auto;
      padding: 2.5rem 1.5rem;
    }
    .hero-features-list,
    .hero-description {
      display: none;
    }
    .hero-headline {
      font-size: 1.75rem;
      margin-bottom: 0.5rem;
    }
    .auth-form-half {
      padding: 2rem 1.5rem;
    }
  }
</style>
@endsection

@section('content')
<div class="auth-split-wrapper">

  <!-- Left: Hero Branded Section (#D14A28 to #F2A20C Gradient) -->
  <div class="auth-hero-half">
    <div class="auth-hero-pattern"></div>
    <div class="auth-hero-orb auth-hero-orb-1"></div>
    <div class="auth-hero-orb auth-hero-orb-2"></div>

    <div class="hero-brand-header">
      <div class="hero-logo-box p-1" style="background: #ffffff; border-radius: 16px;">
        <img src="{{ asset('assets/logo/abk-bg-transparent.jpeg') }}" alt="ABK Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
      </div>
      <div>
        <h2 class="hero-brand-name">
          {{ config('app.locale') == 'en' ? config('variables.templateName') : config('variables.templateNameAr') }}
        </h2>
        <p class="hero-brand-sub">{{ config('variables.NameSiteAr') }} • مستلزمات البناء</p>
      </div>
    </div>

    <div class="hero-center-content">
      <div class="hero-tag-pill">
        <i class="bx bxs-rocket text-warning"></i>
        <span>إنشاء حساب جديد للموظفين والمشرفين</span>
      </div>

      <h1 class="hero-headline">
        انضم إلى المنظومة الذكية لإدارة الموارد.
      </h1>

      <p class="hero-description">
        سجل حسابك للبدء في إدارة المستودعات، تتبع حركة الشاحنات، تسجيل المشتريات وإصدار الفواتير الدقيقة بكل سلاسة.
      </p>

      <div class="hero-features-list">
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-check"></i></div>
          <span>صلاحيات مخصصة لكل مستخدم</span>
        </div>
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-shield-quarter"></i></div>
          <span>أمان متقدم وسجلات تدقيق كاملة</span>
        </div>
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-line-chart"></i></div>
          <span>لوحة تحكم إحصائية وتحليلات فورية</span>
        </div>
      </div>
    </div>

    <div class="hero-bottom-footer">
      <span>© {{ date('Y') }} {{ config('variables.templateName') }}. جميع الحقوق محفوظة.</span>
      <span class="d-none d-sm-inline"><i class="bx bx-lock-alt me-1"></i> تشفير آمن 256-bit</span>
    </div>
  </div>

  <!-- Right: Form Section -->
  <div class="auth-form-half">
    <div class="auth-top-actions">
      @if (Session::get('theme') == 'dark')
        <a href="{{ url('theme/light') }}" class="auth-action-btn" title="{{ __('Light Mode') }}">
          <i class="bx bx-sun text-warning"></i>
          <span>{{ __('Light') }}</span>
        </a>
      @else
        <a href="{{ url('theme/dark') }}" class="auth-action-btn" title="{{ __('Dark Mode') }}">
          <i class="bx bx-moon text-primary"></i>
          <span>{{ __('Dark') }}</span>
        </a>
      @endif

      @if (Session::get('locale') == 'ar')
        <a href="{{ url('lang/en') }}" class="auth-action-btn" title="English">
          <i class="bx bx-globe"></i>
          <span>English</span>
        </a>
      @else
        <a href="{{ url('lang/ar') }}" class="auth-action-btn" title="العربية">
          <i class="bx bx-globe"></i>
          <span>العربية</span>
        </a>
      @endif
    </div>

    <div class="auth-form-container">
      <!-- Mobile Brand Logo Header (Visible on smaller screens when hero is hidden) -->
      <div class="d-flex d-lg-none align-items-center gap-3 mb-4 pb-3 border-bottom">
        <div class="p-1 bg-white rounded-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
          <img src="{{ asset('assets/logo/abk-bg-transparent.jpeg') }}" alt="ABK Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
          <h5 class="mb-0 fw-bold">{{ config('app.locale') == 'en' ? config('variables.templateName') : config('variables.templateNameAr') }}</h5>
          <small class="text-muted">{{ config('variables.NameSiteAr') }} • مستلزمات البناء</small>
        </div>
      </div>

      <h2 class="auth-welcome-title">حساب جديد 🚀</h2>
      <p class="auth-welcome-sub">قم بإدخال بياناتك لإنشاء حساب جديد في النظام</p>

      @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: 12px;" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="formAuthentication" action="{{ url('/auth/register-action') }}" method="POST">
        @csrf

        <!-- Username -->
        <div class="auth-field-group">
          <div class="auth-field-header">
            <span>{{ __('Username') }}</span>
          </div>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon"><i class="bx bx-user"></i></span>
            <input 
              type="text" 
              class="auth-input-control" 
              id="username" 
              name="username" 
              value="{{ old('username') }}" 
              placeholder="اسم المستخدم" 
              required 
              autofocus 
            />
          </div>
        </div>

        <!-- Email -->
        <div class="auth-field-group">
          <div class="auth-field-header">
            <span>{{ __('Email') }}</span>
          </div>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon"><i class="bx bx-envelope"></i></span>
            <input 
              type="email" 
              class="auth-input-control" 
              id="email" 
              name="email" 
              value="{{ old('email') }}" 
              placeholder="example@abk.dz" 
              required 
            />
          </div>
        </div>

        <!-- Password -->
        <div class="auth-field-group">
          <div class="auth-field-header">
            <span>{{ __('Password') }}</span>
          </div>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon"><i class="bx bx-lock-alt"></i></span>
            <input 
              type="password" 
              class="auth-input-control" 
              id="password" 
              name="password" 
              placeholder="••••••••••••" 
              required 
            />
            <button type="button" class="auth-password-toggle" id="toggleRegisterPassword" title="إظهار / إخفاء">
              <i class="bx bx-hide" id="registerToggleIcon"></i>
            </button>
          </div>
        </div>

        <!-- Terms -->
        <div class="auth-field-group">
          <label class="auth-checkbox-label" for="terms-conditions">
            <input class="auth-checkbox-input" type="checkbox" id="terms-conditions" name="terms" required />
            <span>أوافق على <a href="javascript:void(0);">سياسة الخصوصية والشروط</a></span>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-submit-button">
          <i class="bx bx-user-check fs-5"></i>
          <span>{{ __('Sign up') }}</span>
        </button>
      </form>

      <div class="auth-form-footer">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ url('auth/login-basic') }}">
          <span>{{ __('Sign in instead') }}</span>
        </a>
      </div>
    </div>

    <div class="text-center py-2" style="font-size: 0.82rem; color: var(--brand-text-body);">
      <span>نظام مشفر ومحمي • عدة بن قصير سفيان</span>
    </div>
  </div>

</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleRegisterPassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('registerToggleIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
      toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          toggleIcon.classList.remove('bx-hide');
          toggleIcon.classList.add('bx-show');
        } else {
          passwordInput.type = 'password';
          toggleIcon.classList.remove('bx-show');
          toggleIcon.classList.add('bx-hide');
        }
      });
    }
  });
</script>
@endsection

