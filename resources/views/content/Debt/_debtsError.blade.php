<div class="card-body p-4 text-center">
  <div class="py-4">
    {{-- Error Logo Container --}}
    <div class="avatar avatar-xl bg-label-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 72px; height: 72px; border: 2px dashed rgba(255, 62, 29, 0.4);">
      <i class="bx bx-shield-x text-danger fs-1"></i>
    </div>
    <h5 class="text-danger fw-bold mb-2">{{ __('حدث خطأ غير متوقع أثناء معالجة البيانات') }}</h5>
    <p class="text-muted mb-3 mx-auto" style="max-width: 540px;">
      {{ __('تعذر جلب السجلات المطلوبة في الوقت الحالي. تم تسجيل تفاصيل هذا الخطأ في ملف السجلات لمراجعته.') }}
    </p>

    {{-- Auto-Generated Error Code Badge with One-Click Copy --}}
    <div class="d-inline-flex align-items-center gap-2 bg-light border border-danger-subtle rounded-pill px-3 py-2 mb-3 shadow-xs">
      <i class="bx bx-barcode fs-5 text-danger"></i>
      <span class="text-muted small fw-semibold">{{ __('رمز الخطأ التلقائي:') }}</span>
      <span class="badge bg-danger text-white fs-6 font-monospace auto-error-code-label">{{ $errorId ?? $errorCode ?? 'ERR-UNKNOWN' }}</span>
      <button type="button" class="btn btn-xs btn-outline-danger rounded-pill copy-error-code-btn" data-code="{{ $errorId ?? $errorCode ?? 'ERR-UNKNOWN' }}" title="{{ __('نسخ رمز الخطأ') }}">
        <i class="bx bx-copy"></i>
        <span class="copy-btn-text">{{ __('نسخ') }}</span>
      </button>
    </div>

    <div class="text-muted small mb-4">
      <i class="bx bx-info-circle me-1 text-primary"></i>
      <span>{{ __('للبحث السريع عن تفاصيل الخطأ، ابحث عن الرمز أعلاه داخل:') }}</span>
      <code class="text-dark bg-light px-2 py-1 rounded">storage/logs/laravel.log</code>
    </div>

    <div class="d-flex justify-content-center gap-2">
      <button type="button" class="btn btn-outline-primary debt-search-retry-btn d-inline-flex align-items-center gap-1 shadow-sm">
        <i class="bx bx-refresh fs-5"></i>
        <span>{{ __('إعادة المحاولة') }}</span>
      </button>
    </div>
  </div>
</div>
