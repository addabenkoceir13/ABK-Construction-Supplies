<?php

namespace App\Services\Debt;

class DebtPaymentCalculator
{
    /**
     * Compute the debt fields resulting from applying a payment.
     *
     * Mirrors payDebt()'s original four-branch logic exactly:
     *  - the payment alone covers the full total -> paid, using the raw
     *    total (not debt_paid + payment)
     *  - debt_paid + payment equals the total -> paid
     *  - debt_paid + payment is under the total -> partial payment
     *  - debt_paid + payment exceeds the total -> false (caller shows an
     *    error and must not apply any change)
     *
     * @return array|false
     */
    public function calculate($totalDebtAmount, $debtPaidSoFar, $restDebtAmount, $amountPaid)
    {
        if ($amountPaid == $totalDebtAmount) {
            return [
                'status' => config('constant.DEBTS_STATUS.PAID'),
                'debt_paid' => $amountPaid,
                'rest_debt_amount' => $totalDebtAmount - $amountPaid,
                'date_end_debt' => now()->format('Y-m-d'),
            ];
        }

        if (($debtPaidSoFar + $amountPaid) == $totalDebtAmount) {
            return [
                'status' => config('constant.DEBTS_STATUS.PAID'),
                'debt_paid' => $debtPaidSoFar + $amountPaid,
                'rest_debt_amount' => $restDebtAmount - $amountPaid,
                'date_end_debt' => now()->format('Y-m-d'),
            ];
        }

        if (($debtPaidSoFar + $amountPaid) < $totalDebtAmount) {
            return [
                'debt_paid' => $debtPaidSoFar + $amountPaid,
                'rest_debt_amount' => $restDebtAmount - $amountPaid,
                'date_end_debt' => now()->format('Y-m-d'),
            ];
        }

        return false;
    }
}
