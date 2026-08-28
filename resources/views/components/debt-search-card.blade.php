@props(['action', 'name' => null, 'phone' => null, 'resultCount' => null])

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ $action }}" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label for="search-debt-name" class="form-label">{{ __('الاسم الكامل') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-user"></i></span>
          <input type="text" id="search-debt-name" name="name" class="form-control" value="{{ $name }}" placeholder="{{ __('ابحث بالاسم الكامل') }}">
        </div>
      </div>
      <div class="col-md-5">
        <label for="search-debt-phone" class="form-label">{{ __('رقم الهاتف') }}</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="bx bx-phone"></i></span>
          <input type="text" id="search-debt-phone" name="phone" class="form-control" value="{{ $phone }}" placeholder="{{ __('ابحث برقم الهاتف') }}">
        </div>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">{{ __('بحث') }}</button>
        <a href="{{ $action }}" class="btn btn-outline-secondary flex-fill">{{ __('مسح') }}</a>
      </div>
    </form>

    @if ($name || $phone)
      <div class="mt-3 text-muted">
        @if ($name)
          <span class="badge bg-label-primary me-1">{{ __('الاسم') }}: {{ $name }}</span>
        @endif
        @if ($phone)
          <span class="badge bg-label-primary me-1">{{ __('الهاتف') }}: {{ $phone }}</span>
        @endif
        <span>{{ __('عدد النتائج') }}: {{ $resultCount }}</span>
      </div>
    @endif
  </div>
</div>
