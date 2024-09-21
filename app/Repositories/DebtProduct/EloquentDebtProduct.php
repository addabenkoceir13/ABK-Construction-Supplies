<?php

namespace App\Repositories\DebtProduct;

use App\Models\DebtProduct;

class EloquentDebtProduct implements DebtProductRepository
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return DebtProduct::all();
    }
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return DebtProduct::find($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $DebtProduct = DebtProduct::create($data);

        return $DebtProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $DebtProduct = $this->find($id);

        $DebtProduct->update($data);

        return $DebtProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $DebtProduct = $this->find($id);

        return $DebtProduct->delete();
    }

    /**
     * @param $perPage
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function paginate($perPage, $search = null)
    {
        $query = DebtProduct::query();

        $result = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }
}
