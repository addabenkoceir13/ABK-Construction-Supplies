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

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (response) { return response.text(); })
      .then(function (html) {
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
      })
      .catch(function (err) {
        console.error('Search error:', err);
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
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.text(); })
            .then(function (html) {
              region.innerHTML = html;
              window.history.replaceState(null, '', url);
              if (window.bootstrap && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(region.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) {
                  new bootstrap.Tooltip(el);
                });
              }
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
