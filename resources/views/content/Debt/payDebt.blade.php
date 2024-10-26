<!-- Modal -->
<div class="modal fade" id="PayDebtModal{{ $debt->id }}"  tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Pay a debt') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('debt.pay', $debt->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class="bx bx-user mx-1"></i>{{ __('Customer Name') }} : </small>
                <span class="me-2">{{ $debt->fullname }}</span>
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date first Debt') }} : </small>
                <span>{{ $debt->date_debut_debt }}</span>
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Total debt') }} : </small>
                <span>{{ number_format($debt->total_debt_amount,2)  }} {{ __('DZ') }}</span>
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-calendar mx-1'></i>{{ __('Date last debt') }} : </small>
                <span>{{ is_null($debt->date_end_debt) ? '   /   ' : $debt->date_end_debt }}</span>
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Remaining debt') }} : </small>
                <span>{{ is_null($debt->rest_debt_amount) ? 0.00 : number_format($debt->rest_debt_amount, 2) }} {{ __('DZ') }}</span>
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <h5>
                <small class="text-light fw-semibold"><i class='bx bx-money mx-1'></i>{{ __('Debt paid') }} : </small>
                <span>{{ is_null($debt->debt_paid) ? 0.00 : number_format($debt->debt_paid, 2) }} {{ __('DZ') }}</span>
              </h5>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">{{ __('Amount') }}</label>
              <input type="number" id="total-value" name="debt_paid" class="form-control total-value" placeholder="{{ __('Amount') }}" required>
              {{ __('Total Amount:') }} <span class="modal-total-amount" id="modal-total-amount">0.00</span>
            </div>
            <div class="col-md-6">
              @foreach ($debt->getDebtProduct  as $item)
              <div class="form-check mt-3">
                <input
                  class="form-check-input debt-checkbox"
                  type="checkbox"
                  name="id_debt_product[]"
                  data-amount="{{ $item->amount }}"
                  data-row-id="{{ $debt->id }}"
                  value="{{ $item->id }}"
                  id="Check-{{ $item->id }}"
                  {{ $item->status == 0 ? '' : 'checked disabled' }}
                />
                <label class="form-check-label" for="Check-{{ $item->id }}">
                    {{ $item->name_category }} | {{ number_format($item->amount, 2) }} {{ __('DZ') }}
                </label>
              </div>
              @endforeach
              </div>
            </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-outline-primary">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
