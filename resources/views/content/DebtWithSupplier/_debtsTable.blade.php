<div class="table-responsive text-nowrap" data-search-total="{{ $debts->total() }}">
  <table id="datatable-debt" class="table table-hover is-stripedt">
    <thead>
        <tr>
            <th >#</th>
            <th >{{ $tractorDriverLabel }}</th>
            <th >{{ __('Name') }}</th>
            <th >{{ __('Phone') }}</th>
            <th >{{ __('Debts') }}</th>
            <th >{{ __('Create At') }}</th>
            <th >{{ __('Status') }}</th>
            <th >{{ __('Action') }}</th>
        </tr>

    </thead>
    <tbody>
      @foreach ($debts as $debt)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $debt->tractorDriver->fullname }}</td>
          <td>{{ $debt->fullname }}</td>
          <td>{{ $debt->phone }}</td>
          <td>
            <table class="table table-sm table-bordered">
              <tbody>
                @foreach ($debt->getDebtProduct  as $item)
                  <tr  >
                    <td>{{ $item->name_category }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->amount }} {{ __('DZ') }}</td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show me-1"></i> {{ __('Show') }}</a>
                          <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> {{ __('Edit') }}</a>
                          <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> {{ __('Delete') }}</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
                <tr>
                  <td colspan="2">{{ __('Total') }}</td>
                  <td>{{ $debt->total_debt_amount }} {{ __('DZ') }}</td>
                  <td></td>
                </tr>
            </tbody>
            </table>

          </td>
          <td>{{ $debt->date_debut_debt }}</td>
          <td>
            @if ($debt->status == 'paid')
              <span class="badge bg-label-success me-1">{{ __('Paid') }}</span>
            @else
              <span class="badge bg-label-warning me-1">{{ __('Unpaid') }}</span>
            @endif
          </td>
          <td>
            <a href="{{ route('debt-supplier.show', $debt->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-show me-1'></i> <span>{{ __('View Debt') }}</span>">
              <span class="badge bg-label-info"><i class='bx bx-show me-1'></i></span>
            </a>
            <a href="{{ route('debt-supplier.edit', $debt->id) }}" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-edit bx-xs' ></i> <span>{{ __('Modify Debt') }}</span>">
              <span class="badge bg-label-success"><i class="bx bx-edit-alt me-1"></i></span>
            </a>
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeleteDebt{{ $debt->id }}">
              <span class="badge bg-label-danger" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Delete  debt') }}</span>">
              <i class="bx bx-trash me-1"></i></span>
            </a>
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#PayDebtModal{{ $debt->id }}" data-row-id="{{ $debt->id }}">
              <span class="badge bg-label-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-bell bx-xs' ></i> <span>{{ __('Pay a debt') }}</span>">
              <i class='bx bx-money'></i></span>
            </a>
            <a href="{{ route('debt.printer-facteur-client',['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" target="_blank" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="<i class='bx bx-show me-1'></i> <span>{{ __('Print Invoice') }}</span>">
              <span class="badge bg-label-dribbble"><i class='bx bx-printer me-1'></i></span>
            </a>
          </td>
        </tr>
        @include('content.DebtWithSupplier.delete')
        @include('content.DebtWithSupplier.payDebt')
      @endforeach
    </tbody>
    <tfoot>
        {{-- <tr>
          <th >#</th>
            <th >{{ __('Name') }}</th>
            <th >{{ __('Phone') }}</th>
            <th >{{ __('Debts') }}</th>
        </tr> --}}
    </tfoot>
  </table>
  <div class="d-flex justify-content-center mt-3">
    {{ $debts->withQueryString()->links() }}
  </div>
</div>
