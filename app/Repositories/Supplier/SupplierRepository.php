<?php

namespace App\Repositories\Supplier;

interface SupplierRepository
{
    /**
     * Get all available Coupon.
     * @return mixed
     */
    public function all();

    public function SelectSupplier();
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
