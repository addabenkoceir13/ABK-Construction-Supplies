@extends('layouts/contentNavbarLayout')

@section('title', __('Building Materials'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <span class="text-muted fw-light">{{ __('Services') }} /</span> {{ __('Building Materials') }}
    </h4>
    <p class="text-muted mb-0 small">{{ __('Manage material categories, product types, and measurement units.') }}</p>
  </div>
  <div class="d-flex gap-2 mt-2 mt-sm-0">
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddBuilding">
      <i class="bx bx-plus-circle"></i>
      <span>{{ __('Add Building Material') }}</span>
    </button>
  </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Main Categories') }}</span>
          <h4 class="card-title mb-0 fw-bold text-primary">{{ $categories->total() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-cube-alt fs-4"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted small fw-semibold d-block">{{ __('Subcategories / Units') }}</span>
          <h4 class="card-title mb-0 fw-bold text-success">{{ $subcategories->total() }}</h4>
        </div>
        <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
          <i class="bx bx-layer fs-4"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 1. Building Materials Categories Table Card -->
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-cube-alt fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Material Categories') }}</h5>
        <small class="text-muted">{{ __('Total Categories:') }} <strong class="text-primary">{{ $categories->total() }}</strong></small>
      </div>
    </div>
  </div>

  @include('content.Category.create')

  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th><i class="bx bx-cube me-1 text-primary"></i>{{ __('Material Name') }}</th>
          <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Created At') }}</th>
          <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $category)
          <tr>
            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                  {{ strtoupper(mb_substr($category->name, 0, 1)) }}
                </div>
                <span class="fw-semibold text-heading">{{ $category->name }}</span>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center text-muted small">
                <i class="bx bx-calendar me-1 text-primary"></i>
                <span>{{ $category->created_at->format('d-m-Y') }}</span>
              </div>
            </td>
            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="btn btn-icon btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditBuilding-{{ $category->id }}" title="{{ __('Modify building materials') }}">
                  <i class="bx bx-edit-alt"></i>
                </button>
                <button type="button" class="btn btn-icon btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBuilding-{{ $category->id }}" title="{{ __('Delete building material') }}">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @include('content.Category.edit')
        @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">
              <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
              {{ __('No building materials found') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center py-3 border-top gap-2">
    <div class="small text-muted">
      {{ __('Showing') }} <span class="fw-semibold">{{ $categories->firstItem() ?? 0 }}</span> {{ __('to') }} <span class="fw-semibold">{{ $categories->lastItem() ?? 0 }}</span> {{ __('of') }} <span class="fw-semibold">{{ $categories->total() }}</span>
    </div>
    <div class="pagination-wrapper mb-0">
      {{ $categories->links() }}
    </div>
  </div>
</div>

<!-- 2. Subcategories Table Card -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar avatar-sm bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
        <i class="bx bx-layer fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 fw-semibold">{{ __('Subcategories & Units') }}</h5>
        <small class="text-muted">{{ __('Total Subcategories:') }} <strong class="text-success">{{ $subcategories->total() }}</strong></small>
      </div>
    </div>
  </div>

  <div class="table-responsive text-nowrap">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th><i class="bx bx-cube me-1 text-primary"></i>{{ __('Parent Category') }}</th>
          <th><i class="bx bx-tag me-1 text-primary"></i>{{ __('Unit / Subcategory') }}</th>
          <th><i class="bx bx-calendar me-1 text-primary"></i>{{ __('Created At') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subcategories as $subcategory)
          <tr>
            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
            <td>
              <span class="badge bg-label-primary rounded-pill px-3 py-2">
                <i class="bx bx-cube-alt me-1"></i>{{ $subcategory->getCategory->name ?? '-' }}
              </span>
            </td>
            <td>
              <span class="fw-semibold text-heading">{{ $subcategory->name }}</span>
            </td>
            <td>
              <div class="d-flex align-items-center text-muted small">
                <i class="bx bx-calendar me-1 text-primary"></i>
                <span>{{ $subcategory->created_at->format('d-m-Y') }}</span>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4 text-muted">
              <i class="bx bx-folder-open fs-2 d-block mb-1"></i>
              {{ __('No subcategories found') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center py-3 border-top gap-2">
    <div class="small text-muted">
      {{ __('Showing') }} <span class="fw-semibold">{{ $subcategories->firstItem() ?? 0 }}</span> {{ __('to') }} <span class="fw-semibold">{{ $subcategories->lastItem() ?? 0 }}</span> {{ __('of') }} <span class="fw-semibold">{{ $subcategories->total() }}</span>
    </div>
    <div class="pagination-wrapper mb-0">
      {{ $subcategories->links() }}
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
@endsection
