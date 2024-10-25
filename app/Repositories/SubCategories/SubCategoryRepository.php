<?php

namespace App\Repositories\SubCategories;

interface SubCategoryRepository
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

    public function get($id);

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
