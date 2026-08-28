<?php

namespace App\Services\Debt;

use App\Repositories\Debt\DebtRepository;
use App\Repositories\DebtProduct\DebtProductRepository;

class DebtService
{
    public function __construct(
        private DebtRepository $debt,
        private DebtProductRepository $debtProduct,
    ) {
    }

    /**
     * Create a debt together with its products, and set the debt's total
     * from the sum of the product amounts.
     *
     * @param array $products name_product[]
     * @param array $quantities quantity[]
     * @param array $amounts amount_due[]
     * @param array $dateDebts date_debt[]
     * @param array $subcategoryIds subcategory_ids[]
     */
    public function createWithProducts(
        array $dataDebt,
        array $products,
        array $quantities,
        array $amounts,
        array $dateDebts,
        array $subcategoryIds
    ) {
        $total = 0;

        $debt = $this->debt->create($dataDebt);

        foreach ($products as $index => $product) {
            $subcategory_id = $subcategoryIds[$index];
            $quantity = $quantities[$index];
            $amount = $amounts[$index];
            $dateDebt = $dateDebts[$index];
            $total += $amount;

            $dataDebtProduct = array_replace([
                'debt_id' => $debt->id,
                'subcategory_id' => $subcategory_id,
                'name_category' => $products[$index],
                'quantity' => $quantity,
                'amount' => $amount,
                'date_debt' => $dateDebt,
            ]);

            $this->debtProduct->create($dataDebtProduct);
        }

        $dataDebtTotal = array_replace([
            'total_debt_amount' => $total,
            'rest_debt_amount' => $total,
        ]);

        return $this->debt->update($debt->id, $dataDebtTotal);
    }

    /**
     * Update a debt's core fields, then upsert its products by id (id 0
     * means "new row"), and recompute the debt's total/rest amounts.
     *
     * @param array $products name_product[]
     * @param array $quantities quantity[]
     * @param array $amounts amount_due[]
     * @param array $dateDebts date_debt[]
     * @param array $subcategoryIds subcategory_ids[]
     * @param array $ids id[] — existing DebtProduct id, or 0 for a new row
     */
    public function updateWithProducts(
        $id,
        array $dataDebt,
        array $products,
        array $quantities,
        array $amounts,
        array $dateDebts,
        array $subcategoryIds,
        array $ids
    ) {
        $total = 0;

        $debt = $this->debt->update($id, $dataDebt);

        $TotalDebtAmount = $debt->total_debt_amount;
        $restDebtAmount = $debt->rest_debt_amount;
        $debtPaid = $debt->debt_paid;

        for ($index = 0; $index < count($products); $index++) {
            $subcategory_id = $subcategoryIds[$index];
            $quantity = $quantities[$index];
            $amount = $amounts[$index];
            $dateDebt = $dateDebts[$index];
            $idOld = $ids[$index];

            $total += $amount;

            $dataDebtProduct = [
                'subcategory_id' => $subcategory_id,
                'name_category' => $products[$index],
                'quantity' => $quantities[$index],
                'amount' => $amounts[$index],
                'date_debt' => $dateDebts[$index],
            ];

            if ($idOld == 0) {
                $dataDebtProduct['debt_id'] = $id;
                $this->debtProduct->create($dataDebtProduct);
            } else {
                $dataDebtProduct = array_replace([
                    'subcategory_id' => $subcategory_id,
                    'name_category' => $products[$index],
                    'quantity' => $quantities[$index],
                    'amount' => $amounts[$index],
                    'date_debt' => $dateDebts[$index],
                ]);
                $this->debtProduct->update($idOld, $dataDebtProduct);
            }
        }

        $restDebtAmountNew = $total - $debtPaid;

        $dataDebtTotal = array_replace([
            'total_debt_amount' => $total,
            'rest_debt_amount' => $restDebtAmountNew,
        ]);

        return $this->debt->update($id, $dataDebtTotal);
    }
}
