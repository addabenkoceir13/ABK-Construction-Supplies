<!-- Modal Create Supplier Debt -->
<div class="modal fade" id="modalAddDebt" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width: 1140px;">
    <div class="modal-content shadow-2xl border-0 overflow-hidden custom-debt-modal {{ Session::get('theme') == 'dark' ? 'theme-dark' : 'theme-light' }}">
      <form action="{{ route('debt-supplier.store') }}" method="POST" id="formAddSupplierDebt" class="needs-validation d-flex flex-column h-100 w-100 m-0">
        @csrf

        {{-- Modal Top Accent Bar --}}
        <div class="modal-top-accent modal-top-accent-supplier flex-shrink-0"></div>

        {{-- Modal Header --}}
        <div class="modal-header border-bottom py-3 px-4 flex-shrink-0">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center shadow-xs modal-header-icon">
              <i class="bx bxs-truck fs-3 text-info"></i>
            </div>
            <div>
              <div class="d-flex align-items-center gap-2">
                <h5 class="modal-title mb-0 fw-bold text-heading">{{ __('إضافة سجل دين مع المورد / السائق') }}</h5>
                <span class="badge bg-label-info rounded-pill px-2 py-1 fs-tiny product-count-badge">
                  <i class="bx bx-package me-1"></i><span id="modal-product-counter">1</span> {{ __('سلعة') }}
                </span>
              </div>
              <small class="text-muted">{{ __('توثيق استلام وتسليم المواد والديون الخاصة بالموردين والسائقين') }}</small>
            </div>
          </div>
          <button type="button" class="btn-close shadow-none modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        {{-- Modal Body --}}
        <div class="modal-body p-4 custom-modal-body flex-grow-1 overflow-auto">
          {{-- SECTION 1: Supplier & Customer Information Card --}}
          <div class="section-card customer-section-card mb-4 p-3 rounded-3 border shadow-2xs">
            <div class="d-flex align-items-center gap-2 mb-3 text-info">
              <i class="bx bx-id-card fs-5"></i>
              <h6 class="mb-0 fw-bold text-uppercase fs-tiny tracking-wider">{{ __('بيانات المورد والزبون') }}</h6>
            </div>
            
            <div class="row g-3">
              {{-- Tractor Driver / Supplier --}}
              <div class="col-md-6 col-lg-3">
                <label for="tractor_driver_id" class="form-label text-heading small fw-bold mb-1">
                  {{ __('المورد / سائق الجرار') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-merge custom-input-group">
                  <span class="input-group-text border-end-0"><i class="bx bxs-truck text-info"></i></span>
                  <select class="form-select border-start-0 ps-1 custom-select-styled" id="tractor_driver_id" name="tractor_driver_id" required>
                    <option value="">{{ __('اختر المورد أو السائق...') }}</option>
                    @foreach ($suppliers as $supplier)
                      <option value="{{ $supplier->id }}">{{ $supplier->fullname }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Customer Name --}}
              <div class="col-md-6 col-lg-3">
                <label for="fullname" class="form-label text-heading small fw-bold mb-1">
                  {{ __('اسم الزبون') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-merge custom-input-group">
                  <span class="input-group-text border-end-0"><i class="bx bx-user text-info"></i></span>
                  <input type="text" id="fullname" name="fullname" class="form-control border-start-0 ps-1 @error('fullname') is-invalid @enderror" placeholder="{{ __('اسم الزبون المعني بالدين') }}" required />
                </div>
                @error('fullname')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Phone Number --}}
              <div class="col-md-6 col-lg-3">
                <label for="phone" class="form-label text-heading small fw-bold mb-1">
                  {{ __('رقم الهاتف') }}
                </label>
                <div class="input-group input-group-merge custom-input-group">
                  <span class="input-group-text border-end-0"><i class="bx bx-phone text-info"></i></span>
                  <input type="tel" id="phone" name="phone" class="form-control border-start-0 ps-1 phone-mask @error('phone') is-invalid @enderror" placeholder="0655 44 33 22" />
                </div>
                @error('phone')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Debt Date --}}
              <div class="col-md-6 col-lg-3">
                <label for="date_debut_debt" class="form-label text-heading small fw-bold mb-1">
                  {{ __('تاريخ بداية الدين') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-merge custom-input-group">
                  <span class="input-group-text border-end-0"><i class='bx bx-calendar-check text-info'></i></span>
                  <input type="date" id="date_debut_debt" name="date_debut_debt" class="form-control border-start-0 ps-1 @error('date_debut_debt') is-invalid @enderror" min="2020-01-01" value="{{ $dateToday }}" required />
                </div>
                @error('date_debut_debt')
                  <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
              </div>

              {{-- Notes --}}
              <div class="col-12">
                <label for="note" class="form-label text-heading small fw-bold mb-1">
                  {{ __('ملاحظات إضافية') }}
                </label>
                <div class="input-group input-group-merge custom-input-group">
                  <span class="input-group-text border-end-0"><i class="bx bx-notepad text-muted"></i></span>
                  <input type="text" name="note" id="note" class="form-control border-start-0 ps-1" placeholder="{{ __('ملاحظة أو وصف اختياري...') }}" />
                </div>
              </div>
            </div>
          </div>

          {{-- SECTION 2: Dynamic Products List Header --}}
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
              <div class="badge bg-label-info p-2 rounded-3"><i class="bx bx-cube-alt text-info fs-5"></i></div>
              <div>
                <h6 class="mb-0 fw-bold text-heading">{{ __('المواد والسلع المسلمة') }}</h6>
                <small class="text-muted">{{ __('حدد المواد، الكميات، والمبالغ المستحقة') }}</small>
              </div>
            </div>
            <button type="button" id="add-product-create" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 add-item-btn shadow-xs">
              <i class="bx bx-plus-circle fs-6"></i>
              <span>{{ __('إضافة مادة أخرى') }}</span>
            </button>
          </div>

          {{-- Dynamic Product Cards Container --}}
          <div id="product-container-create" class="d-flex flex-column gap-3 mb-4">
            <div class="product-row-create product-item-card p-3 rounded-3 border position-relative" data-row-index="1">
              <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-bottom-divider">
                <span class="badge rounded-pill px-2 py-1 fs-tiny fw-semibold item-badge-num">
                  <i class="bx bx-cube text-info me-1"></i>{{ __('المادة رقم') }} <span class="row-num">1</span>
                </span>
                <button type="button" class="btn btn-icon btn-xs btn-outline-danger remove-row-create rounded-circle" title="{{ __('حذف هذه المادة') }}">
                  <i class="bx bx-trash fs-6"></i>
                </button>
              </div>

              <div class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('اسم المنتوج / الفئة') }} <span class="text-danger">*</span></label>
                  <select class="form-select name-product custom-select-styled" name="name_product[]" required>
                    <option value="">{{ __('اختر المنتوج...') }}</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->name }}" data-id="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 col-lg-3 inpute-create">
                  {{-- Will be populated dynamically via subcategory ajax --}}
                </div>
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('المبلغ المستحق') }} <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge custom-input-group">
                    <span class="input-group-text border-end-0 text-success fw-bold">DZ</span>
                    <input type="number" class="form-control border-start-0 ps-1 amount-due-input" name="amount_due[]" min="0" step="0.01" placeholder="1000" required>
                    <span class="input-group-text text-muted">.00</span>
                  </div>
                </div>
                <div class="col-md-6 col-lg-3">
                  <label class="form-label text-muted small fw-semibold mb-1">{{ __('تاريخ الدين') }} <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge custom-input-group">
                    <span class="input-group-text border-end-0"><i class='bx bx-calendar text-muted'></i></span>
                    <input type="date" name="date_debt[]" class="form-control border-start-0 ps-1" min="2020-01-01" value="{{ $dateToday }}" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- SECTION 3: Live Real-Time Total Preview Card --}}
          <div class="live-total-preview-card p-3 rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border">
            <div class="d-flex align-items-center gap-2">
              <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                <i class="bx bx-calculator fs-5 text-success"></i>
              </div>
              <div>
                <div class="text-heading small fw-bold">{{ __('المجموع الإجمالي للسلع:') }}</div>
                <div class="small text-muted">{{ __('يتم تحديث المجموع تلقائياً أثناء كتابة المبالغ') }}</div>
              </div>
            </div>
            <div class="text-end">
              <div class="live-total-amount fw-bolder fs-3 font-monospace tracking-tight" id="modal-live-total-display">0.00 <span class="fs-6 fw-bold live-total-currency">DZ</span></div>
            </div>
          </div>
        </div>

        {{-- Modal Footer: Always Visible and Locked to Bottom --}}
        <div class="modal-footer border-top py-3 px-4 flex-shrink-0 d-flex justify-content-between align-items-center custom-modal-footer">
          <button type="button" class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2" data-bs-dismiss="modal">
            <i class="bx bx-x fs-5"></i>
            <span>{{ __('إلغاء') }}</span>
          </button>
          <button type="submit" class="btn btn-info px-5 py-2 shadow-sm d-flex align-items-center gap-2 fw-semibold save-debt-submit-btn text-white">
            <i class="bx bx-check-circle fs-5"></i>
            <span>{{ __('حفظ الدين الآن') }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Scoped CSS with 100% Theme/Dark-Mode Compatibility --}}
<style>
  .custom-debt-modal {
    border-radius: 1.1rem !important;
    max-height: calc(100vh - 3.5rem);
    animation: modalScaleUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  .modal-top-accent-supplier {
    height: 5px;
    background: linear-gradient(90deg, #03c3ec 0%, #696cff 50%, #71dd37 100%);
  }

  .custom-modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
  }

  .custom-modal-footer {
    position: sticky;
    bottom: 0;
    z-index: 1050;
  }

  .product-item-card {
    transition: all 0.25s ease-in-out;
    border-inline-start: 4px solid #03c3ec !important;
    animation: productCardIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  .add-item-btn {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .add-item-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(3, 195, 236, 0.25) !important;
  }

  .remove-row-create {
    transition: transform 0.2s ease;
  }

  .remove-row-create:hover {
    transform: scale(1.15) rotate(8deg);
  }

  .product-card-removing {
    animation: productCardOut 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  /* ========================================================
     --- DARK MODE RULES ---
     Applies when html has .dark-style, [data-bs-theme="dark"],
     or modal has .theme-dark
     ======================================================== */
  .dark-style .custom-debt-modal,
  [data-bs-theme="dark"] .custom-debt-modal,
  .custom-debt-modal.theme-dark {
    background-color: #2b2c40 !important;
    color: #cbc8e0 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
  }

  .dark-style .custom-debt-modal .modal-header,
  [data-bs-theme="dark"] .custom-debt-modal .modal-header,
  .custom-debt-modal.theme-dark .modal-header {
    background-color: #232333 !important;
    border-color: #444564 !important;
  }

  .dark-style .custom-debt-modal .custom-modal-footer,
  [data-bs-theme="dark"] .custom-debt-modal .custom-modal-footer,
  .custom-debt-modal.theme-dark .custom-modal-footer {
    background-color: #232333 !important;
    border-color: #444564 !important;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.25) !important;
  }

  .dark-style .custom-debt-modal .customer-section-card,
  [data-bs-theme="dark"] .custom-debt-modal .customer-section-card,
  .custom-debt-modal.theme-dark .customer-section-card {
    background-color: rgba(255, 255, 255, 0.03) !important;
    border-color: #444564 !important;
  }

  .dark-style .custom-debt-modal .product-item-card,
  [data-bs-theme="dark"] .custom-debt-modal .product-item-card,
  .custom-debt-modal.theme-dark .product-item-card {
    background-color: #32344d !important;
    border-color: #444564 !important;
    color: #cbc8e0 !important;
  }

  .dark-style .custom-debt-modal .product-item-card:hover,
  [data-bs-theme="dark"] .custom-debt-modal .product-item-card:hover,
  .custom-debt-modal.theme-dark .product-item-card:hover {
    background-color: #373955 !important;
    border-color: #03c3ec !important;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35) !important;
  }

  .dark-style .custom-debt-modal .custom-input-group .input-group-text,
  [data-bs-theme="dark"] .custom-debt-modal .custom-input-group .input-group-text,
  .custom-debt-modal.theme-dark .custom-input-group .input-group-text,
  .dark-style .custom-debt-modal .unit-addon,
  [data-bs-theme="dark"] .custom-debt-modal .unit-addon,
  .custom-debt-modal.theme-dark .unit-addon {
    background-color: #32344d !important;
    border-color: #444564 !important;
    color: #a1acb8 !important;
  }

  .dark-style .custom-debt-modal .custom-input-group .form-control,
  .dark-style .custom-debt-modal .custom-select-styled,
  [data-bs-theme="dark"] .custom-debt-modal .custom-input-group .form-control,
  [data-bs-theme="dark"] .custom-debt-modal .custom-select-styled,
  .custom-debt-modal.theme-dark .custom-input-group .form-control,
  .custom-debt-modal.theme-dark .custom-select-styled {
    background-color: #2b2c40 !important;
    border-color: #444564 !important;
    color: #cbc8e0 !important;
  }

  .dark-style .custom-debt-modal .custom-input-group .form-control:focus,
  .dark-style .custom-debt-modal .custom-select-styled:focus,
  [data-bs-theme="dark"] .custom-debt-modal .custom-input-group .form-control:focus,
  [data-bs-theme="dark"] .custom-debt-modal .custom-select-styled:focus,
  .custom-debt-modal.theme-dark .custom-input-group .form-control:focus,
  .custom-debt-modal.theme-dark .custom-select-styled:focus {
    background-color: #2b2c40 !important;
    border-color: #03c3ec !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 0.2rem rgba(3, 195, 236, 0.2) !important;
  }

  .dark-style .custom-debt-modal .custom-select-styled option,
  [data-bs-theme="dark"] .custom-debt-modal .custom-select-styled option,
  .custom-debt-modal.theme-dark .custom-select-styled option {
    background-color: #2b2c40 !important;
    color: #cbc8e0 !important;
  }

  .dark-style .custom-debt-modal .live-total-preview-card,
  [data-bs-theme="dark"] .custom-debt-modal .live-total-preview-card,
  .custom-debt-modal.theme-dark .live-total-preview-card {
    background: linear-gradient(135deg, rgba(113, 221, 55, 0.12) 0%, rgba(3, 195, 236, 0.12) 100%) !important;
    border-color: rgba(113, 221, 55, 0.3) !important;
  }

  .dark-style .custom-debt-modal .live-total-preview-card .live-total-amount,
  [data-bs-theme="dark"] .custom-debt-modal .live-total-preview-card .live-total-amount,
  .custom-debt-modal.theme-dark .live-total-preview-card .live-total-amount {
    color: #71dd37 !important;
  }

  .dark-style .custom-debt-modal .live-total-preview-card .live-total-currency,
  [data-bs-theme="dark"] .custom-debt-modal .live-total-preview-card .live-total-currency,
  .custom-debt-modal.theme-dark .live-total-preview-card .live-total-currency {
    color: #a1acb8 !important;
  }

  .dark-style .custom-debt-modal .item-badge-num,
  [data-bs-theme="dark"] .custom-debt-modal .item-badge-num,
  .custom-debt-modal.theme-dark .item-badge-num {
    background-color: #232333 !important;
    color: #a1acb8 !important;
    border: 1px solid #444564 !important;
  }

  .dark-style .custom-debt-modal .border-bottom-divider,
  [data-bs-theme="dark"] .custom-debt-modal .border-bottom-divider,
  .custom-debt-modal.theme-dark .border-bottom-divider {
    border-color: #444564 !important;
  }

  .dark-style .custom-debt-modal .modal-close-btn,
  [data-bs-theme="dark"] .custom-debt-modal .modal-close-btn,
  .custom-debt-modal.theme-dark .modal-close-btn {
    background-color: #32344d !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.4) !important;
    border: 1px solid #444564 !important;
    opacity: 0.9;
  }
  .dark-style .custom-debt-modal .modal-close-btn:hover,
  [data-bs-theme="dark"] .custom-debt-modal .modal-close-btn:hover,
  .custom-debt-modal.theme-dark .modal-close-btn:hover {
    background-color: #373955 !important;
    opacity: 1;
    transform: scale(1.05);
  }

  .dark-style .custom-debt-modal input[type="date"]::-webkit-calendar-picker-indicator,
  [data-bs-theme="dark"] .custom-debt-modal input[type="date"]::-webkit-calendar-picker-indicator,
  .custom-debt-modal.theme-dark input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8);
  }

  .dark-style .custom-debt-modal ::placeholder,
  [data-bs-theme="dark"] .custom-debt-modal ::placeholder,
  .custom-debt-modal.theme-dark ::placeholder {
    color: #7071a4 !important;
    opacity: 1;
  }

  .dark-style .custom-modal-body::-webkit-scrollbar,
  [data-bs-theme="dark"] .custom-modal-body::-webkit-scrollbar,
  .custom-debt-modal.theme-dark .custom-modal-body::-webkit-scrollbar {
    width: 6px;
  }
  .dark-style .custom-modal-body::-webkit-scrollbar-thumb,
  [data-bs-theme="dark"] .custom-modal-body::-webkit-scrollbar-thumb,
  .custom-debt-modal.theme-dark .custom-modal-body::-webkit-scrollbar-thumb {
    background: #444564;
    border-radius: 4px;
  }
  .dark-style .custom-modal-body::-webkit-scrollbar-track,
  [data-bs-theme="dark"] .custom-modal-body::-webkit-scrollbar-track,
  .custom-debt-modal.theme-dark .custom-modal-body::-webkit-scrollbar-track {
    background: #232333;
  }

  /* ========================================================
     --- LIGHT MODE RULES ---
     Applies when html has .light-style, [data-bs-theme="light"],
     or modal has .theme-light
     ======================================================== */
  .light-style .custom-debt-modal,
  [data-bs-theme="light"] .custom-debt-modal,
  .custom-debt-modal.theme-light,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) {
    background-color: #ffffff !important;
    color: #566a7f !important;
  }

  .light-style .custom-debt-modal .modal-header,
  [data-bs-theme="light"] .custom-debt-modal .modal-header,
  .custom-debt-modal.theme-light .modal-header,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .modal-header {
    background-color: #f8f9fa !important;
    border-color: #e7e7e8 !important;
  }

  .light-style .custom-debt-modal .custom-modal-footer,
  [data-bs-theme="light"] .custom-debt-modal .custom-modal-footer,
  .custom-debt-modal.theme-light .custom-modal-footer,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-modal-footer {
    background-color: #f8f9fa !important;
    border-color: #e7e7e8 !important;
    box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.04) !important;
  }

  .light-style .custom-debt-modal .customer-section-card,
  [data-bs-theme="light"] .custom-debt-modal .customer-section-card,
  .custom-debt-modal.theme-light .customer-section-card,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .customer-section-card {
    background-color: #fbfbfd !important;
    border-color: #e7e7e8 !important;
  }

  .light-style .custom-debt-modal .product-item-card,
  [data-bs-theme="light"] .custom-debt-modal .product-item-card,
  .custom-debt-modal.theme-light .product-item-card,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .product-item-card {
    background-color: #ffffff !important;
    border-color: #e7e7e8 !important;
    color: #566a7f !important;
  }

  .light-style .custom-debt-modal .product-item-card:hover,
  [data-bs-theme="light"] .custom-debt-modal .product-item-card:hover,
  .custom-debt-modal.theme-light .product-item-card:hover,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .product-item-card:hover {
    box-shadow: 0 4px 14px rgba(67, 89, 113, 0.08) !important;
    border-color: #03c3ec !important;
  }

  .light-style .custom-debt-modal .custom-input-group .input-group-text,
  [data-bs-theme="light"] .custom-debt-modal .custom-input-group .input-group-text,
  .custom-debt-modal.theme-light .custom-input-group .input-group-text,
  .light-style .custom-debt-modal .unit-addon,
  [data-bs-theme="light"] .custom-debt-modal .unit-addon,
  .custom-debt-modal.theme-light .unit-addon,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-input-group .input-group-text,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .unit-addon {
    background-color: #f8f9fa !important;
    border-color: #d9dee3 !important;
    color: #697a8d !important;
  }

  .light-style .custom-debt-modal .custom-input-group .form-control,
  .light-style .custom-debt-modal .custom-select-styled,
  [data-bs-theme="light"] .custom-debt-modal .custom-input-group .form-control,
  [data-bs-theme="light"] .custom-debt-modal .custom-select-styled,
  .custom-debt-modal.theme-light .custom-input-group .form-control,
  .custom-debt-modal.theme-light .custom-select-styled,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-input-group .form-control,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-select-styled {
    background-color: #ffffff !important;
    border-color: #d9dee3 !important;
    color: #566a7f !important;
  }

  .light-style .custom-debt-modal .custom-input-group .form-control:focus,
  .light-style .custom-debt-modal .custom-select-styled:focus,
  [data-bs-theme="light"] .custom-debt-modal .custom-input-group .form-control:focus,
  [data-bs-theme="light"] .custom-debt-modal .custom-select-styled:focus,
  .custom-debt-modal.theme-light .custom-input-group .form-control:focus,
  .custom-debt-modal.theme-light .custom-select-styled:focus,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-input-group .form-control:focus,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .custom-select-styled:focus {
    background-color: #ffffff !important;
    border-color: #03c3ec !important;
    box-shadow: 0 0 0 0.2rem rgba(3, 195, 236, 0.15) !important;
  }

  .light-style .custom-debt-modal .live-total-preview-card,
  [data-bs-theme="light"] .custom-debt-modal .live-total-preview-card,
  .custom-debt-modal.theme-light .live-total-preview-card,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .live-total-preview-card {
    background: linear-gradient(135deg, rgba(113, 221, 55, 0.08) 0%, rgba(3, 195, 236, 0.05) 100%) !important;
    border-color: rgba(113, 221, 55, 0.25) !important;
  }

  .light-style .custom-debt-modal .live-total-preview-card .live-total-amount,
  [data-bs-theme="light"] .custom-debt-modal .live-total-preview-card .live-total-amount,
  .custom-debt-modal.theme-light .live-total-preview-card .live-total-amount,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .live-total-preview-card .live-total-amount {
    color: #71dd37 !important;
  }

  .light-style .custom-debt-modal .live-total-preview-card .live-total-currency,
  [data-bs-theme="light"] .custom-debt-modal .live-total-preview-card .live-total-currency,
  .custom-debt-modal.theme-light .live-total-preview-card .live-total-currency,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .live-total-preview-card .live-total-currency {
    color: #566a7f !important;
  }

  .light-style .custom-debt-modal .item-badge-num,
  [data-bs-theme="light"] .custom-debt-modal .item-badge-num,
  .custom-debt-modal.theme-light .item-badge-num,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .item-badge-num {
    background-color: #f2f2f6 !important;
    color: #566a7f !important;
  }

  .light-style .custom-debt-modal .border-bottom-divider,
  [data-bs-theme="light"] .custom-debt-modal .border-bottom-divider,
  .custom-debt-modal.theme-light .border-bottom-divider,
  html:not(.dark-style):not([data-bs-theme="dark"]) .custom-debt-modal:not(.theme-dark) .border-bottom-divider {
    border-color: #ebebef !important;
  }

  .light-style .custom-modal-body::-webkit-scrollbar,
  [data-bs-theme="light"] .custom-modal-body::-webkit-scrollbar,
  .custom-debt-modal.theme-light .custom-modal-body::-webkit-scrollbar {
    width: 6px;
  }
  .light-style .custom-modal-body::-webkit-scrollbar-thumb,
  [data-bs-theme="light"] .custom-modal-body::-webkit-scrollbar-thumb,
  .custom-debt-modal.theme-light .custom-modal-body::-webkit-scrollbar-thumb {
    background: #d9dee3;
    border-radius: 4px;
  }
  .light-style .custom-modal-body::-webkit-scrollbar-track,
  [data-bs-theme="light"] .custom-modal-body::-webkit-scrollbar-track,
  .custom-debt-modal.theme-light .custom-modal-body::-webkit-scrollbar-track {
    background: #f8f9fa;
  }

  @keyframes modalScaleUp {
    0% { transform: scale(0.95); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
  }

  @keyframes productCardIn {
    0% { opacity: 0; transform: translateY(14px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
  }

  @keyframes productCardOut {
    0% { opacity: 1; transform: translateY(0) scale(1); max-height: 180px; }
    100% { opacity: 0; transform: translateY(-12px) scale(0.95); max-height: 0; padding-top: 0; padding-bottom: 0; margin-bottom: 0; }
  }
</style>
