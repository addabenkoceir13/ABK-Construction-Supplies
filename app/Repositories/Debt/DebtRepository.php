<?php

namespace App\Repositories\Debt;

interface DebtRepository
{
    /**
     * Get all available Coupon.
     * @return mixed
     */
    public function all();

    public function getSupplier();

    public function debtPaid();

    public function debtUnPaid();

    public function driverDebtPaid();

    public function driverDebtUnPaid();

    /**
     * {@inheritdoc}
     */
    public function find($id);

    /**
     * {@inheritdoc}
     */
    public function create(array $data);
    /**
     * {@inheritdoc}
     */
    public function update($id, array $data);

    /**
     * {@inheritdoc}
     */
    public function delete($id);

    /**
     * Paginate Coupons.
     *
     * @param $perPage
     * @param $search
     * @return mixed
     */
    public function paginate($perPage, $search = null);
}
