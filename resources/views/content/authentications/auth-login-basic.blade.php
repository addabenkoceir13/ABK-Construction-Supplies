@extends('layouts/blankLayout')

@section('title', __('Login Basic'))

@section('page-style')
<style>
  /* ----------------------------------------------------
   * A.B.K Construction Supplies - Split-Screen Auth Theme
   * Palette: Primary #D14A28, Warning #F2A20C, Info #4E9CC0
   * ---------------------------------------------------- */
  :root {
    --brand-primary: #D14A28;
    --brand-primary-hover: #b63e1f;
    --brand-accent: #F2A20C;
    --brand-info: #4E9CC0;
    --brand-primary-glow: rgba(209, 74, 40, 0.35);
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

  /* ----------------------------------------------------
   * Hero Visual Half (Solid Primary & Orange Gradient)
   * ---------------------------------------------------- */
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

  /* Dynamic Geometric Construction Pattern */
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

  /* ----------------------------------------------------
   * Form Content Half
   * ---------------------------------------------------- */
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
    margin-bottom: 1.35rem;
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

  .auth-forgot-link {
    color: #D14A28;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: color 0.2s ease;
  }

  .auth-forgot-link:hover {
    color: #F2A20C;
    text-decoration: underline;
  }

  .auth-options-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    font-size: 0.9rem;
  }

  .auth-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--brand-text-body);
    user-select: none;
  }

  .auth-checkbox-input {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    accent-color: #D14A28;
    cursor: pointer;
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
  }

  .auth-submit-button:hover {
    background: #b63e1f;
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -4px rgba(209, 74, 40, 0.55);
  }

  .auth-submit-button:active {
    transform: translateY(0);
  }

  /* Feedback Alerts */
  .auth-alert {
    padding: 0.9rem 1.15rem;
    border-radius: 12px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.5rem;
  }
  .auth-alert-danger {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
  }
  .auth-alert-warning {
    background: rgba(242, 162, 12, 0.14);
    border: 1px solid rgba(242, 162, 12, 0.35);
    color: #d97706;
  }
  .auth-alert-success {
    background: rgba(16, 185, 129, 0.14);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #10b981;
  }

  /* Lockout Card */
  .auth-lockout-box {
    background: rgba(239, 68, 68, 0.08);
    border: 1.5px solid rgba(239, 68, 68, 0.3);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
  }
  .lockout-progress-container {
    width: 100%;
    height: 6px;
    background: rgba(239, 68, 68, 0.15);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px;
  }
  .lockout-progress-fill {
    height: 100%;
    background: #D14A28;
    transition: width 1s linear;
  }

  .auth-form-footer {
    text-align: center;
    font-size: 0.85rem;
    color: var(--brand-text-body);
    padding-top: 1.5rem;
  }

  /* Responsive Split Collapse */
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

    <!-- Header Brand Identification -->
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

    <!-- Center Highlights & Value Props -->
    <div class="hero-center-content">
      <div class="hero-tag-pill">
        <i class="bx bxs-badge-check text-warning"></i>
        <span>المنصة الرائدة لإدارة مواد البناء والنقل اللوجستي</span>
      </div>

      <h1 class="hero-headline">
        سرعة، دقة، وتحكم كامل في عملياتك التشغيلية.
      </h1>

      <p class="hero-description">
        حلول ذكية متكاملة لإدارة إمدادات البناء، أساطيل الشاحنات، حسابات الموردين والديون، مع لوحة تحكم فورية ودقيقة.
      </p>

      <div class="hero-features-list">
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-check"></i></div>
          <span>متابعة فورية للمخزون ومواد البناء</span>
        </div>
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-shield-quarter"></i></div>
          <span>أمان متقدم وحماية مشفرة لكافة السجلات والبيانات</span>
        </div>
        <div class="hero-feature-item">
          <div class="hero-feature-icon"><i class="bx bx-receipt"></i></div>
          <span>فواتير دقيقة ومزامنة مستمرة للديون والمدفوعات</span>
        </div>
      </div>
    </div>

    <!-- Bottom Footer Note -->
    <div class="hero-bottom-footer">
      <span>© {{ date('Y') }} {{ config('variables.templateName') }}. جميع الحقوق محفوظة.</span>
      <span class="d-none d-sm-inline"><i class="bx bx-lock-alt me-1"></i> تشفير 256-bit SSL</span>
    </div>
  </div>

  <!-- Right: Form Section -->
  <div class="auth-form-half">
    <!-- Top Action Toggles -->
    <div class="auth-top-actions">
      <!-- Theme Switcher -->
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

      <!-- Language Switcher -->
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

    <!-- Main Authentication Form Card -->
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

      <h2 class="auth-welcome-title">{{ __('Welcome') }}! 👋</h2>
      <p class="auth-welcome-sub">{{ __('Please sign-in to your account') }}</p>

      <!-- Feedback / Alert Notifications -->
      @php
        $activeLockoutSeconds = session('lockout_seconds', $lockoutSeconds ?? 0);
        $isLockout = session('lockout', false) || $activeLockoutSeconds > 0;
      @endphp

      @if ($isLockout)
        <div class="auth-lockout-box" id="lockoutBanner" role="alert">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bx bxs-lock-alt text-danger" style="font-size: 1.5rem;"></i>
            <strong class="text-danger">تم حظر تسجيل الدخول مؤقتاً!</strong>
          </div>
          <p class="mb-2 text-muted" style="font-size: 0.88rem; line-height: 1.5;">
            {{ session('error') ?? 'تم تجاوز الحد الأقصى للمحاولات. تم تجميد تسجيل الدخول لحماية الحساب.' }}
          </p>
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bx bx-time-five text-warning"></i>
            <span style="font-size: 0.88rem;">الوقت المتبقي: </span>
            <strong id="lockoutCountdownText" class="text-warning">01:30</strong>
          </div>
          <div class="lockout-progress-container">
            <div class="lockout-progress-fill" id="lockoutProgressBar" style="width: 100%;"></div>
          </div>
        </div>
      @elseif (session('remaining_attempts'))
        <div class="auth-alert auth-alert-warning" role="alert">
          <i class="bx bx-shield-quarter fs-5"></i>
          <div><strong>{{ session('error') }}</strong></div>
        </div>
      @elseif (session('error'))
        <div class="auth-alert auth-alert-danger" role="alert">
          <i class="bx bx-error-circle fs-5"></i>
          <div>{{ session('error') }}</div>
        </div>
      @elseif ($errors->any())
        <div class="auth-alert auth-alert-danger" role="alert">
          <i class="bx bx-error-circle fs-5"></i>
          <div>
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        </div>
      @elseif (session('success'))
        <div class="auth-alert auth-alert-success" role="alert">
          <i class="bx bx-check-circle fs-5"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      <!-- Authentication Form -->
      <form id="formAuthentication" action="{{ url('/auth/login-action') }}" method="POST" autocomplete="on">
        @csrf

        <!-- Email Field -->
        <div class="auth-field-group">
          <div class="auth-field-header">
            <span>{{ __('Email') }}</span>
          </div>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon"><i class="bx bx-envelope"></i></span>
            <input 
              type="email" 
              class="auth-input-control @error('email') border-danger @enderror" 
              id="email" 
              name="email" 
              value="{{ old('email') }}" 
              placeholder="{{ __('Enter your email') }}" 
              required 
              autocomplete="email" 
              autofocus
            />
          </div>
        </div>

        <!-- Password Field -->
        <div class="auth-field-group">
          <div class="auth-field-header">
            <span>{{ __('Password') }}</span>
            <a href="{{ url('auth/forgot-password-basic') }}" class="auth-forgot-link">
              {{ __('Forgot Password?') }}
            </a>
          </div>
          <div class="auth-input-wrapper">
            <span class="auth-input-icon"><i class="bx bx-lock-alt"></i></span>
            <input 
              type="password" 
              class="auth-input-control @error('password') border-danger @enderror" 
              id="password" 
              name="password" 
              placeholder="••••••••••••" 
              required 
              autocomplete="current-password"
            />
            <button type="button" class="auth-password-toggle" id="togglePasswordBtn" title="إظهار / إخفاء كلمة المرور">
              <i class="bx bx-hide" id="passwordToggleIcon"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="auth-options-bar">
          <label class="auth-checkbox-label" for="remember-me">
            <input class="auth-checkbox-input" type="checkbox" id="remember-me" name="remember" value="1" checked />
            <span>{{ __('Remember Me') }}</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button class="auth-submit-button" type="submit" id="btnSubmitLogin">
          <i class="bx bx-log-in-circle fs-5"></i>
          <span id="btnSubmitText">{{ __('Sign in') }}</span>
          <div class="spinner-border spinner-border-sm text-light d-none" id="btnSubmitSpinner" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </button>
      </form>
    </div>

    <!-- Security Footer -->
    <div class="auth-form-footer">
      <i class="bx bx-shield-quarter text-warning me-1"></i>
      <span>نظام مشفر ومحمي • عدة بن قصير سفيان</span>
    </div>
  </div>

</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Interactive Password Show/Hide Toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');

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

    // Submit Button Loading State
    const authForm = document.getElementById('formAuthentication');
    const submitBtn = document.getElementById('btnSubmitLogin');
    const submitText = document.getElementById('btnSubmitText');
    const submitSpinner = document.getElementById('btnSubmitSpinner');

    if (authForm && submitBtn) {
      authForm.addEventListener('submit', function () {
        if (authForm.checkValidity()) {
          setTimeout(function () {
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.85';
            if (submitSpinner) submitSpinner.classList.remove('d-none');
            if (submitText) submitText.textContent = 'جاري التحقق...';
          }, 50);
        }
      });
    }

    // Web Audio API Unlock Chime
    function playUnlockChime() {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const notes = [
          { freq: 659.25, time: 0, dur: 0.35, gain: 0.22 },
          { freq: 830.61, time: 0.12, dur: 0.45, gain: 0.25 },
          { freq: 987.77, time: 0.24, dur: 0.75, gain: 0.3 }
        ];

        notes.forEach(n => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.type = 'triangle';
          osc.frequency.setValueAtTime(n.freq, ctx.currentTime + n.time);
          gain.gain.setValueAtTime(n.gain, ctx.currentTime + n.time);
          gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + n.time + n.dur);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(ctx.currentTime + n.time);
          osc.stop(ctx.currentTime + n.time + n.dur);
        });
      } catch (err) {
        console.log('Audio chime not supported:', err);
      }
    }

    // Real-Time Decrementing Countdown & Auto-Reflash
    let lockoutSecs = {{ (int) $activeLockoutSeconds }};
    const maxLockoutSecs = {{ (int) ($activeLockoutSeconds > 0 ? $activeLockoutSeconds : (config('auth.login_throttle.decay_seconds', 90))) }};

    if (lockoutSecs > 0) {
      const emailInput = document.getElementById('email');
      const passwordInput = document.getElementById('password');
      const submitBtn = document.getElementById('btnSubmitLogin');
      const submitText = document.getElementById('btnSubmitText');
      const timerDisplay = document.getElementById('lockoutCountdownText');
      const progressBar = document.getElementById('lockoutProgressBar');
      const lockoutBanner = document.getElementById('lockoutBanner');

      if (emailInput) emailInput.disabled = true;
      if (passwordInput) passwordInput.disabled = true;
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('disabled');
        submitBtn.style.pointerEvents = 'none';
        submitBtn.style.opacity = '0.65';
      }

      function formatTime(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return `${m < 10 ? '0' : ''}${m}:${sec < 10 ? '0' : ''}${sec}`;
      }

      function stepTimer() {
        if (timerDisplay) timerDisplay.textContent = formatTime(lockoutSecs);
        if (submitText) submitText.textContent = `مغلق مؤقتاً (${formatTime(lockoutSecs)}) 🔒`;
        if (progressBar) {
          const pct = Math.max(0, (lockoutSecs / maxLockoutSecs) * 100);
          progressBar.style.width = `${pct}%`;
        }

        if (lockoutSecs <= 0) {
          clearInterval(lockoutInterval);
          playUnlockChime();

          if (lockoutBanner) {
            lockoutBanner.className = 'auth-alert auth-alert-success';
            lockoutBanner.innerHTML = '<i class="bx bx-check-circle fs-5"></i><div><strong>انتهت فترة الحظر!</strong> يمكنك المحاولة الآن. جاري تنشيط النموذج...</div>';
          }

          if (emailInput) emailInput.disabled = false;
          if (passwordInput) passwordInput.disabled = false;
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('disabled');
            submitBtn.style.pointerEvents = 'auto';
            submitBtn.style.opacity = '1';
          }
          if (submitText) submitText.textContent = '{{ __("Sign in") }}';

          setTimeout(() => {
            window.location.href = "{{ url('/auth/login-basic') }}";
          }, 1200);
          return;
        }

        lockoutSecs--;
      }

      stepTimer();
      const lockoutInterval = setInterval(stepTimer, 1000);
    }
  });
</script>
@endsection
