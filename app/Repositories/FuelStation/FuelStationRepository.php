<?php

namespace App\Repositories\FuelStation;

interface FuelStationRepository
{
    /**
     * Get all available Coupon.
     * @return mixed
     */
    public function all();

    /**
     * {@inheritdoc}
     */
    public function find($id);

    public function updateStatus($ids, $status);

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
    public function paginate($perPage,  $search = null, $start_date = null, $end_date = null);

    /**
     * Paginate Coupons.
     *
     * @param $perPage
     * @param $search
     * @return mixed
     */
    public function paginatePaid($perPage, $search = null, $start_date = null, $end_date = null);
}
