@props(['action', 'name' => null, 'phone' => null, 'resultCount' => null, 'target' => '#debts-table-region'])

<div class="card mb-4 shadow-sm border-0">
  <div class="card-header bg-transparent pb-1 pt-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-filter-alt fs-6"></i>
      </div>
      <h6 class="card-title mb-0 fw-semibold">{{ __('Search & Filter') }}</h6>
    </div>
    <div class="search-loader d-none">
      <div class="spinner-border spinner-border-sm text-primary" role="status">
        <span class="visually-hidden">{{ __('Loading...') }}</span>
      </div>
    </div>
  </div>

  <div class="card-body pt-2">
    <form method="GET" action="{{ $action }}" class="debt-search-form row g-3 align-items-end" data-target="{{ $target }}">
      <div class="col-md-5 col-sm-6">
        <label for="search-debt-name" class="form-label text-muted small fw-semibold mb-1">{{ __('Customer Name') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text bg-lighter"><i class="bx bx-user text-muted"></i></span>
          <input type="text" id="search-debt-name" name="name" class="form-control debt-search-input" value="{{ $name }}" placeholder="{{ __('Search by customer name...') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <label for="search-debt-phone" class="form-label text-muted small fw-semibold mb-1">{{ __('Phone Number') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text bg-lighter"><i class="bx bx-phone text-muted"></i></span>
          <input type="text" id="search-debt-phone" name="phone" class="form-control debt-search-input" value="{{ $phone }}" placeholder="{{ __('Search by phone number...') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-md-3 col-sm-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill d-flex align-items-center justify-content-center gap-1">
          <i class="bx bx-search"></i>
          <span>{{ __('Search') }}</span>
        </button>
        <button type="button" class="btn btn-outline-secondary debt-search-clear" data-bs-toggle="tooltip" title="{{ __('Reset Filters') }}">
          <i class="bx bx-refresh"></i>
        </button>
      </div>
    </form>

    <div class="debt-search-summary mt-3 pt-2 border-top d-flex flex-wrap align-items-center justify-content-between gap-2" @if (!$name && !$phone) style="display:none" @endif>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="small text-muted fw-semibold">{{ __('Active Filters:') }}</span>
        <span class="debt-search-summary-name" @if (!$name) style="display:none" @endif>
          <span class="badge bg-label-primary rounded-pill d-inline-flex align-items-center gap-1 px-3 py-2">
            <i class="bx bx-user fs-7"></i>
            <span>{{ __('Name') }}: <strong class="debt-search-summary-name-value">{{ $name }}</strong></span>
          </span>
        </span>
        <span class="debt-search-summary-phone" @if (!$phone) style="display:none" @endif>
          <span class="badge bg-label-info rounded-pill d-inline-flex align-items-center gap-1 px-3 py-2">
            <i class="bx bx-phone fs-7"></i>
            <span>{{ __('Phone') }}: <strong class="debt-search-summary-phone-value">{{ $phone }}</strong></span>
          </span>
        </span>
      </div>
      <div class="d-flex align-items-center">
        <span class="badge bg-label-secondary rounded-pill px-3 py-2">
          <i class="bx bx-list-ul me-1"></i>
          {{ __('Results found') }}: <strong class="debt-search-result-count ms-1">{{ $resultCount }}</strong>
        </span>
      </div>
    </div>
  </div>
</div>

@once
<script>
(function () {
  var DEBOUNCE_MS = 350;
  var debounceTimer = null;

  function setLoader(card, show) {
    var loader = card.querySelector('.search-loader');
    if (loader) {
      if (show) {
        loader.classList.remove('d-none');
      } else {
        loader.classList.add('d-none');
      }
    }
  }

  function updateSummary(form, name, phone) {
    var card = form.closest('.card');
    var summary = card.querySelector('.debt-search-summary');
    var nameEl = card.querySelector('.debt-search-summary-name');
    var phoneEl = card.querySelector('.debt-search-summary-phone');

    if (name) {
      nameEl.style.display = '';
      nameEl.querySelector('.debt-search-summary-name-value').textContent = name;
    } else {
      nameEl.style.display = 'none';
    }

    if (phone) {
      phoneEl.style.display = '';
      phoneEl.querySelector('.debt-search-summary-phone-value').textContent = phone;
    } else {
      phoneEl.style.display = 'none';
    }

    summary.style.display = (name || phone) ? 'flex' : 'none';
  }

  function notifyError(message, errorId) {
    var fullMsg = message || 'حدث خطأ غير متوقع أثناء معالجة الطلب.';
    var title = 'خطأ في النظام';
    if (errorId) {
      fullMsg += '<div class="mt-2 d-flex align-items-center gap-1"><span class="badge bg-danger">رمز الخطأ: ' + errorId + '</span><small class="text-white ms-1">(ابحث عنه في السجلات)</small></div>';
    }
    if (window.toastr) {
      toastr.error(fullMsg, title, {
        timeOut: 10000,
        closeButton: true,
        progressBar: true,
        escapeHtml: false
      });
    }
  }

  function renderErrorState(errorId, customMsg) {
    var code = errorId || ('ERR-' + Math.random().toString(36).substring(2, 8).toUpperCase());
    var msg = customMsg || 'تعذر جلب السجلات المطلوبة في الوقت الحالي. تم تسجيل تفاصيل هذا الخطأ في ملف السجلات لمراجعته.';
    return `
      <div class="card-body p-4 text-center">
        <div class="py-4">
          <div class="avatar avatar-xl bg-label-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 72px; height: 72px; border: 2px dashed rgba(255, 62, 29, 0.4);">
            <i class="bx bx-shield-x text-danger fs-1"></i>
          </div>
          <h5 class="text-danger fw-bold mb-2">حدث خطأ غير متوقع أثناء معالجة البيانات</h5>
          <p class="text-muted mb-3 mx-auto" style="max-width: 540px;">${msg}</p>
          <div class="d-inline-flex align-items-center gap-2 bg-light border border-danger-subtle rounded-pill px-3 py-2 mb-3 shadow-xs">
            <i class="bx bx-barcode fs-5 text-danger"></i>
            <span class="text-muted small fw-semibold">رمز الخطأ التلقائي:</span>
            <span class="badge bg-danger text-white fs-6 font-monospace">${code}</span>
            <button type="button" class="btn btn-xs btn-outline-danger rounded-pill copy-error-code-btn" data-code="${code}" title="نسخ رمز الخطأ">
              <i class="bx bx-copy"></i>
              <span class="copy-btn-text">نسخ</span>
            </button>
          </div>
          <div class="text-muted small mb-4">
            <i class="bx bx-info-circle me-1 text-primary"></i>
            <span>للبحث السريع عن تفاصيل الخطأ، ابحث عن الرمز أعلاه داخل:</span>
            <code class="text-dark bg-light px-2 py-1 rounded">storage/logs/laravel.log</code>
          </div>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-outline-primary debt-search-retry-btn d-inline-flex align-items-center gap-1 shadow-sm">
              <i class="bx bx-refresh fs-5"></i>
              <span>إعادة المحاولة</span>
            </button>
          </div>
        </div>
      </div>
    `;
  }

  function runSearch(form) {
    var action = form.getAttribute('action');
    var targetSelector = form.dataset.target;
    var region = document.querySelector(targetSelector);
    var card = form.closest('.card');

    if (!region) {
      form.submit();
      return;
    }

    setLoader(card, true);

    var formData = new FormData(form);
    var params = new URLSearchParams();
    formData.forEach(function (value, key) {
      if (value !== '') {
        params.append(key, value);
      }
    });

    var url = action + (params.toString() ? '?' + params.toString() : '');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html, application/json' } })
      .then(function (response) {
        if (!response.ok) {
          return response.text().then(function (text) {
            var errorId = 'ERR-' + Math.random().toString(36).substring(2, 8).toUpperCase();
            var errorMessage = 'حدث خطأ غير متوقع أثناء معالجة الطلب.';
            var htmlError = null;

            try {
              var json = JSON.parse(text);
              if (json.error_id) errorId = json.error_id;
              if (json.message) errorMessage = json.message;
              if (json.html) htmlError = json.html;
            } catch (e) {
              // Not valid JSON
            }

            notifyError(errorMessage, errorId);
            region.innerHTML = htmlError || renderErrorState(errorId, errorMessage);
          });
        }

        return response.text().then(function (html) {
          region.innerHTML = html;
          var countEl = card.querySelector('.debt-search-result-count');
          if (countEl) {
            var match = html.match(/data-search-total="(\d+)"/);
            if (match) {
              countEl.textContent = match[1];
            }
          }
          updateSummary(form, params.get('name') || '', params.get('phone') || '');
          window.history.replaceState(null, '', url);

          // Re-initialize any bootstrap tooltips
          if (window.bootstrap && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(region.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (el) {
              new bootstrap.Tooltip(el);
            });
          }
        });
      })
      .catch(function (err) {
        console.error('Search error:', err);
        var fallbackId = 'ERR-' + Math.random().toString(36).substring(2, 8).toUpperCase();
        notifyError('تعذر الاتصال بالخادم، يرجى التحقق من الشبكة.', fallbackId);
        region.innerHTML = renderErrorState(fallbackId, 'تعذر الاتصال بالخادم.');
      })
      .finally(function () {
        setLoader(card, false);
      });
  }

  document.addEventListener('input', function (e) {
    if (!e.target.classList || !e.target.classList.contains('debt-search-input')) {
      return;
    }
    var form = e.target.closest('.debt-search-form');
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      runSearch(form);
    }, DEBOUNCE_MS);
  });

  document.addEventListener('submit', function (e) {
    if (!e.target.classList || !e.target.classList.contains('debt-search-form')) {
      return;
    }
    e.preventDefault();
    clearTimeout(debounceTimer);
    runSearch(e.target);
  });

  document.addEventListener('click', function (e) {
    var copyBtn = e.target.closest('.copy-error-code-btn');
    if (copyBtn) {
      var code = copyBtn.dataset.code;
      if (code) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(code).then(function () {
            var btnText = copyBtn.querySelector('.copy-btn-text');
            if (btnText) btnText.textContent = 'تم النسخ!';
            setTimeout(function () {
              if (btnText) btnText.textContent = 'نسخ';
            }, 2000);
            if (window.toastr) {
              toastr.info('تم نسخ رمز الخطأ: ' + code + ' بنجاح إلى الحافظة.', 'تم النسخ');
            }
          });
        } else {
          // Fallback for older browsers
          var tempInput = document.createElement('input');
          tempInput.value = code;
          document.body.appendChild(tempInput);
          tempInput.select();
          document.execCommand('copy');
          document.body.removeChild(tempInput);
          var btnText = copyBtn.querySelector('.copy-btn-text');
          if (btnText) btnText.textContent = 'تم النسخ!';
          setTimeout(function () {
            if (btnText) btnText.textContent = 'نسخ';
          }, 2000);
          if (window.toastr) {
            toastr.info('تم نسخ رمز الخطأ: ' + code + ' بنجاح.', 'تم النسخ');
          }
        }
      }
      return;
    }

    var retryBtn = e.target.closest('.debt-search-retry-btn');
    if (retryBtn) {
      var form = document.querySelector('.debt-search-form');
      if (form) {
        runSearch(form);
      }
      return;
    }

    var clearBtn = e.target.closest('.debt-search-clear');
    if (clearBtn) {
      var form = clearBtn.closest('.card').querySelector('.debt-search-form');
      form.querySelectorAll('.debt-search-input').forEach(function (input) {
        input.value = '';
      });
      clearTimeout(debounceTimer);
      runSearch(form);
      return;
    }

    var paginationLink = e.target.closest('.pagination a');
    if (paginationLink) {
      var region = paginationLink.closest('#debts-table-region, #supplier-debts-table-region, [id$="-table-region"]');
      if (region) {
        e.preventDefault();
        var url = paginationLink.getAttribute('href');
        if (url && url !== '#') {
          var card = document.querySelector('.debt-search-form')?.closest('.card');
          if (card) setLoader(card, true);
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html, application/json' } })
            .then(function (response) {
              if (!response.ok) {
                return response.text().then(function (text) {
                  var errorId = 'ERR-' + Math.random().toString(36).substring(2, 8).toUpperCase();
                  var errorMessage = 'حدث خطأ غير متوقع أثناء تحميل الصفحة.';
                  var htmlError = null;
                  try {
                    var json = JSON.parse(text);
                    if (json.error_id) errorId = json.error_id;
                    if (json.message) errorMessage = json.message;
                    if (json.html) htmlError = json.html;
                  } catch (e) {}

                  notifyError(errorMessage, errorId);
                  region.innerHTML = htmlError || renderErrorState(errorId, errorMessage);
                });
              }
              return response.text().then(function (html) {
                region.innerHTML = html;
                window.history.replaceState(null, '', url);
                if (window.bootstrap && bootstrap.Tooltip) {
                  var tooltipTriggerList = [].slice.call(region.querySelectorAll('[data-bs-toggle="tooltip"]'));
                  tooltipTriggerList.forEach(function (el) {
                    new bootstrap.Tooltip(el);
                  });
                }
              });
            })
            .catch(function (err) {
              window.location.href = url;
            })
            .finally(function () {
              if (card) setLoader(card, false);
            });
        }
      }
    }
  });
})();
</script>
@endonce
