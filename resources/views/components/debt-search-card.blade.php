@props(['action', 'name' => null, 'phone' => null, 'resultCount' => null, 'target' => '#debts-table-region'])

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ $action }}" class="debt-search-form row g-2 align-items-end" data-target="{{ $target }}">
      <div class="col-md-5">
        <label for="search-debt-name" class="form-label">{{ __('الاسم الكامل') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-user"></i></span>
          <input type="text" id="search-debt-name" name="name" class="form-control debt-search-input" value="{{ $name }}" placeholder="{{ __('ابحث بالاسم الكامل') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-md-5">
        <label for="search-debt-phone" class="form-label">{{ __('رقم الهاتف') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-phone"></i></span>
          <input type="text" id="search-debt-phone" name="phone" class="form-control debt-search-input" value="{{ $phone }}" placeholder="{{ __('ابحث برقم الهاتف') }}" autocomplete="off">
        </div>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">{{ __('بحث') }}</button>
        <button type="button" class="btn btn-outline-secondary flex-fill debt-search-clear">{{ __('مسح') }}</button>
      </div>
    </form>

    <div class="debt-search-summary mt-3 text-muted" @if (!$name && !$phone) style="display:none" @endif>
      <span class="debt-search-summary-name" @if (!$name) style="display:none" @endif>
        <span class="badge bg-label-primary me-1">{{ __('الاسم') }}: <span class="debt-search-summary-name-value">{{ $name }}</span></span>
      </span>
      <span class="debt-search-summary-phone" @if (!$phone) style="display:none" @endif>
        <span class="badge bg-label-primary me-1">{{ __('الهاتف') }}: <span class="debt-search-summary-phone-value">{{ $phone }}</span></span>
      </span>
      <span>{{ __('عدد النتائج') }}: <span class="debt-search-result-count">{{ $resultCount }}</span></span>
    </div>
  </div>
</div>

@once
<script>
(function () {
  var DEBOUNCE_MS = 350;
  var debounceTimer = null;

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

    summary.style.display = (name || phone) ? '' : 'none';
  }

  function runSearch(form) {
    var action = form.getAttribute('action');
    var targetSelector = form.dataset.target;
    var region = document.querySelector(targetSelector);
    if (!region) {
      form.submit();
      return;
    }

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
        var card = form.closest('.card');
        var countEl = card.querySelector('.debt-search-result-count');
        if (countEl) {
          var match = html.match(/data-search-total="(\d+)"/);
          if (match) {
            countEl.textContent = match[1];
          }
        }
        updateSummary(form, params.get('name') || '', params.get('phone') || '');
        window.history.replaceState(null, '', url);
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
    if (!e.target.classList || !e.target.classList.contains('debt-search-clear')) {
      return;
    }
    var form = e.target.closest('.debt-search-form');
    form.querySelectorAll('.debt-search-input').forEach(function (input) {
      input.value = '';
    });
    clearTimeout(debounceTimer);
    runSearch(form);
  });
})();
</script>
@endonce
