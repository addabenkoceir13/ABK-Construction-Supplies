@extends('layouts/contentNavbarLayout')

@section('title', __('Building Materials'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Building Materials') }} /</span> {{ __('Building Materials') }}
    </h4>
    <!-- Basic Bootstrap Table -->
    <div class="card p-2 mb-5">
        <h5 class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBuilding">
                {{ __('Add building materials') }}
            </button>
        </h5>
        {{-- @include('content.Category.create') --}}
        <div class="table-responsive text-nowrap">
            <table id="datatable-category" class="table table-hover is-stripedt">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Create At') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->created_at->format('d-m-Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-none" data-bs-toggle="modal"
                                    data-bs-target="#modalEditBuilding-{{ $category->id }}">
                                    <span class="badge bg-label-success" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                        data-bs-placement="top" data-bs-html="true"
                                        title="<i class='bx bx-edit-alt bx-xs' ></i> <span>{{ __('Modify building materials') }}</span>">
                                        <i class="bx bx-edit-alt me-1"></i>
                                    </span>
                                </button>
                                <button type="button" class="btn btn-sm btn-none" data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteBuilding-{{ $category->id }}">
                                    <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                        data-bs-placement="top" data-bs-html="true"
                                        title="<i class='bx bx-trash bx-xs' ></i> <span>{{ __('Delete building material') }}</span>">
                                        <i class="bx bx-trash me-1"></i>
                                    </span>
                                </button>
                                {{-- @include('content.Category.edit') --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->
    <div class="card p-2">
        <h5 class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBuilding">
                {{ __('Add building materials') }}
            </button>
        </h5>
        <div class="table-responsive text-nowrap">
            <table id="datatable-subcategory" class="table table-hover is-stripedt">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type subcategory') }}</th>
                        <th>{{ __('Create At') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subcategories as $subcategory)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subcategory->getCategory->name }}</td>
                            <td>{{ $subcategory->name }}</td>
                            <td>{{ $subcategory->created_at->format('d-m-Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-none" data-bs-toggle="modal"
                                    data-bs-target="#modalEditBuilding{{ $subcategory->id }}">
                                    <span class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
                                </button>
                                <button type="button" class="btn btn-sm btn-none" data-bs-toggle="modal"
                                    data-bs-target="#modalDeleteBuilding{{ $subcategory->id }}">
                                    <span class="badge bg-label-danger"><i class="bx bx-trash me-1"></i></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
    <script>
        new DataTable('#datatable-category', {
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        let column = this;
                        let title = column.footer().textContent;

                        // Create input element
                        let input = document.createElement('input');
                        input.placeholder = title;
                        column.footer().replaceChildren(input);

                        // Event listener for user input
                        input.addEventListener('keyup', () => {
                            if (column.search() !== this.value) {
                                column.search(input.value).draw();
                            }
                        });
                    });
            }
        });
    </script>
    <script>
        new DataTable('#datatable-subcategory', {
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        let column = this;
                        let title = column.footer().textContent;

                        // Create input element
                        let input = document.createElement('input');
                        input.placeholder = title;
                        column.footer().replaceChildren(input);

                        // Event listener for user input
                        input.addEventListener('keyup', () => {
                            if (column.search() !== this.value) {
                                column.search(input.value).draw();
                            }
                        });
                    });
            }
        });
    </script>
@endsection
