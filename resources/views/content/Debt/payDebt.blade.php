<!-- Modal -->
<div class="modal fade" id="PayDebtModal{{ $debt->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header with Gradient Background -->
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel1">
                    <i class='bx bx-credit-card me-2'></i>{{ __('Pay a debt') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('debt.pay', $debt->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <!-- Customer Summary Card -->
                    <div class="card mb-4 border-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h5 class="d-flex align-items-center">
                                        <i class="bx bx-user-circle text-primary me-2"></i>
                                        <strong class="me-2">{{ $debt->fullname }}</strong>
                                    </h5>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-calendar-check text-info me-2'></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('First Debt Date') }}</small>
                                            <span class="fw-semibold">{{ $debt->date_debut_debt }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-money text-danger me-2'></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('Total Debt') }}</small>
                                            <span class="fw-semibold">{{ number_format($debt->total_debt_amount, 2) }} {{ __('DZ') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-calendar text-warning me-2'></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('Last Payment Date') }}</small>
                                            <span class="fw-semibold">{{ is_null($debt->date_end_debt) ? __('Not paid') : $debt->date_end_debt }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-credit-card-front text-success me-2'></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('Remaining Debt') }}</small>
                                            <span class="fw-semibold">{{ is_null($debt->rest_debt_amount) ? 0.0 : number_format($debt->rest_debt_amount, 2) }} {{ __('DZ') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bx-check-circle text-success me-2'></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('Paid Amount') }}</small>
                                            <span class="fw-semibold">{{ is_null($debt->debt_paid) ? 0.0 : number_format($debt->debt_paid, 2) }} {{ __('DZ') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form Section -->
                    <div class="row">
                        <!-- Payment Inputs -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class='bx bx-edit me-2'></i>{{ __('Payment Details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">{{ __('Payment Amount') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-money'></i></span>
                                            <input type="number" id="total-value" name="debt_paid" class="form-control total-value" placeholder="{{ __('Amount') }}" required>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-muted">{{ __('Total Selected:') }}</span>
                                            <span class="modal-total-amount fw-bold text-primary" id="modal-total-amount">0.00 DZ</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="datetime" class="form-label">{{ __('Payment Date') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                            <input type="datetime-local" name="date_payment" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Debt Items Selection -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class='bx bx-list-check me-2'></i>{{ __('Select Debt Items') }}</h6>
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    @foreach ($debt->getDebtProduct as $item)
                                        <div class="form-check mb-2 p-2 border rounded">
                                            <input class="form-check-input debt-checkbox" type="checkbox"
                                                name="id_debt_product[]" data-amount="{{ $item->amount }}"
                                                data-row-id="{{ $debt->id }}" value="{{ $item->id }}"
                                                id="Check-{{ $item->id }}"
                                                {{ $item->status == 0 ? '' : 'checked disabled' }} />
                                            <label class="form-check-label d-flex justify-content-between w-100" for="Check-{{ $item->id }}">
                                                <span>
                                                    <i class='bx bx-package me-1'></i>
                                                    {{ $item->name_category }}
                                                </span>
                                                <span class="badge bg-{{ $item->status == 0 ? 'primary' : 'success' }}">
                                                    {{ number_format($item->amount, 2) }} {{ __('DZ') }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if (!is_null($debt->debtHistories))
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class='bx bx-history me-2'></i>{{ __('Payment History') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($debt->debtHistories as $debtHistory)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class='bx bx-calendar text-secondary me-2'></i>
                                                                {{ $debtHistory->date }}
                                                            </h6>
                                                            <span class="text-muted small">{{ __('Payment') }}</span>
                                                        </div>
                                                        <span class="badge bg-success rounded-pill">
                                                            {{ $debtHistory->amount }} {{ __('DZ') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i>{{ __('Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save me-1'></i>{{ __('Save Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
